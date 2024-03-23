<html>
   <head>
      <title>Invoicin - Verify Organization</title>
      <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   </head>
   <body>

        <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md border -border-gray-300 p-10 rounded-lg">
                @if(isset($success))
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                            <!-- Heroicon name: mini/check-circle -->
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            </div>
                            <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                @if(isset($message))
                                    {{ $message }}
                                @endif
                            </p>
                            </div>
                        </div>
                    </div>
                @endif
                @if(isset($token))
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Verification</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500 mb-10">
                        <p>Click the button below to verify your organization.</p>
                    </div>
                    <form class="space-y-6" action="{{ url('verify-organization') . '/' . $token  }}" method="POST">
                        @csrf
                        <div>
                            <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Verify</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
  
   </body>
</html>