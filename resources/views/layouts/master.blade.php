<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App</title>
<!-- CSS -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

          @stack('style')
</head>
<body>
    <div class="nk-body ui-rounder has-sidebar">
        <div class="nk-app-root">
            <!-- main @s -->
            <div class="nk-main">
                @include('layouts.sidebar')
                <!-- sidebar @e -->
                <!-- wrap @s -->
                <div class="nk-wrap">
                    <!-- main header @s -->
                    @include('layouts.header')
                    <!-- main header @e -->
                    <!-- contain @s -->
                    <!-- contain @e -->
                    <!-- footer @s -->
                    @include('layouts.footer')
                    <!-- footer @e -->
                </div>
                <!-- wrap @e -->
            </div>

        </div>

    </div>
<!-- JavaScript -->

    @stack('scripts')
</body>
</html>
