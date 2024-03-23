<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

trait RequestService
{
    use ApiResponser;
    private Request $originalRequest;
    private array $multipartParams = [];
    private bool $addQuery = true;

    public function forwardRequest($requestUrl, Request $request)
    {
        $this->originalRequest = $request;
        $this->multipartParams = $this->getMultipartParams();

        try {
            $response = $this->getResponse($requestUrl);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $this->errorMessage($response->getBody()->getContents(), $response->getStatusCode());
        }
        return $this->successResponse($response->getBody()->getContents(), $response->getStatusCode());
    }
    

    public function getResponse($url)
    {
        $info = $this->getRequestInfo();
        $http = $this->createHttp($info['type']);

        if ($this->addQuery && $info['query']) {
            $url = $url . '?' . http_build_query($info['query']);
        }
        return $this->call($http, $info['method'], $this->baseUri.$url, $this->getParams($info));
    }

    private function getParams($info)
    {
        $defaultParams = [];
        if ('GET' == $info['method']) {
            return http_build_query($info['query']);
        }
        if ('multipart' == $info['type']) {
            $defaultParams = $this->multipartParams;
        } else {
            $defaultParams = $info['params'];
        }
        if ($info['query']) {
            foreach ($info['query'] as $key => $value) {
                unset($defaultParams[array_search(['name' => $key, 'contents' => $value], $defaultParams)]);
            }
        }

        return $defaultParams;
    }

    private function call(PendingRequest $request, $method, $url, $params)
    {
        switch ($method) {
            case 'HEAD':
                return $request->head($url, $params);
            case 'PATCH':
                return $request->patch($url, $params);
            case 'PUT':
                return $request->put($url, $params);
            case 'DELETE':
                return $request->delete($url, $params);
            case 'POST':
                if (str_contains($this->getRequestInfo()['type'], 'application/x-www-form-urlencoded')) {
                    return $request->withOptions(['form_params' => $params])
                        ->post($url);
                }

                return $request->post($url, $params);
            case 'GET':
            default:
                return $request->get($url, $params);
        }
    }

    private function createHttp($type): PendingRequest
    {
        $headers = [];
        if ($jwt = $this->generateJWT()) {
            $headers['Authorization'] = 'Bearer '.$jwt;
        }
        $request = Http::withHeaders($headers);

        if ($this->originalRequest->isMethod('post') && str_contains($type, 'application/x-www-form-urlencoded')) {
            return $request->asForm()->withoutVerifying();
        }

        $request = match ($type) {
            'multipart' => $request->asMultipart(),
            'form' => $request->asForm(),
            'json', null => $request->asJson(),
            default => $request->contentType($type),
        };

        return $request->withoutVerifying()
            ->withOptions(['decode_content' => false]);
    }

    private function getRequestType(): ?string
    {
        return match (true) {
            $this->originalRequest->isJson() => 'json',
            str_contains($this->originalRequest->header('Content-Type'), 'multipart') => 'multipart',
            str_contains($this->originalRequest->header('Content-Type'), 'application/x-www-form-urlencoded') => 'form',
            default => $this->originalRequest->header('Content-Type')
        };
    }

    private function getRequestInfo(): array
    {
        return [
            'type' => $this->getRequestType(),
            'agent' => $this->originalRequest->userAgent(),
            'method' => $this->originalRequest->method(),
            'token' => $this->originalRequest->bearerToken(),
            'full_url' => $this->originalRequest->fullUrl(),
            'url' => $this->originalRequest->url(),
            'format' => $this->originalRequest->format(),
            'query' => $this->originalRequest->query(),
            'params' => array_replace_recursive($this->originalRequest->post(), $this->originalRequest->allFiles()),
        ];
    }

    private function getMultipartParams()
    {
        $multipartParams = [];
        if ($this->originalRequest->isMethod('post') || $this->originalRequest->isMethod('put')) {
            $formParams = $this->originalRequest->all();
            $multipartParams = [];
            foreach ($formParams as $key => $value) {
                $multipartParams = array_merge($multipartParams, $this->flatten($key, $value));
            }
        }
        return $multipartParams;
    }

    private function flatten($key, $data, $result = [])
    {
        if (!is_array($data)) {
            if ($data instanceof HttpUploadedFile) {
                $result[] = $this->getFileUploadParams($key, $data);
            } else {
                $result[] = ['name' => $key, 'contents' => $data];
            }
            return $result;
        }

        foreach ($data as $subKey => $value) {
            $subKey = $key . '[' . $subKey . ']';
            if (\is_array($value)) {
                $result = $this->flatten($subKey, $value, $result);
            } else {
                if ($value instanceof HttpUploadedFile) {
                    $result[] = $this->getFileUploadParams($subKey, $value);
                    continue;
                }
                $result[] = ['name' => $subKey, 'contents' => $value];
            }
        }

        return $result;
    }

    private function getFileUploadParams($key, $value)
    {
        return [
            'name' => $key,
            'contents' => fopen($value->getRealPath(), 'r'),
            'filename' => $value->getClientOriginalName(),
            'headers' => [
                'Content-Type' => $value->getMimeType(),
            ],
        ];
    }

    private function generateJWT()
    {
        if ( !($user = auth()->user()) ) {
            return false;
        }

        $config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(config('jwt.keys.private')),
            InMemory::plainText(config('jwt.keys.public')),
        );

        $builder = $config->builder()
                // Configures the issuer (iss claim)
                ->issuedBy(env('APP_URL'))
                // Configures the audience (aud claim)
                ->permittedFor($this->baseUri)
                // Configures the time that the token was issue (iat claim)
                ->issuedAt(now()->subMinute()->toDateTimeImmutable())
                // Configures the time that the token can be used (nbf claim)
                ->canOnlyBeUsedAfter(now()->subMinute()->toDateTimeImmutable())
                // Configures the expiration time of the token (exp claim)
                ->expiresAt(now()->addHour(1)->toDateTimeImmutable());

        $payload = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'organization_id' => $user->organization_id
        ];

        foreach($payload as $key => $claim) {
            $builder->withClaim($key, $claim);
        }
                
        return $builder->getToken($config->signer(), $config->signingKey())->toString();
    }
}
