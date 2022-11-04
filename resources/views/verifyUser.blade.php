<html>
   <head>
      <title>Invoicin - Verify User</title>
      <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   </head>
   <body>

        <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <form class="space-y-6" action="{{ url('verify-user') . '/' . $token  }}" method="POST">
                    @csrf
        
                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Verify User</button>
                    </div>
                </form>
            </div>
        </div>
  
   </body>
</html>