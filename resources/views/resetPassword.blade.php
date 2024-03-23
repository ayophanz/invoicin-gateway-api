<html>
   <head>
      <title>Invoicin - Reset Password</title>
      <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   </head>
   <body>

        <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">Update Password</h2>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    @if($errors->any())
                        {!! implode('', $errors->all('<div class="text-red-400">:message</div>')) !!}
                    @endif
                    <form class="space-y-6" action="{{ url('password-reset-link') . '/' . $token  }}" method="POST">
                        @csrf
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <div class="mt-1">
                                <input id="password" name="password" type="password" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 sm:text-sm">
                            </div>
                        </div>
            
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <div class="mt-1">
                                <input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 shadow-sm sm:text-sm">
                            </div> 
                        </div>
            
                        <div>
                            <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
  
   </body>
</html>