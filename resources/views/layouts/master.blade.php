<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LBH</title>
    <link rel="stylesheet" href="/css/app.css">
  </head>
  <body>
    <div id="app">

      @include ('layouts.header')

      <section class="section">
        <div class="container">
          @if (count($errors) > 0)
            <div>
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <router-view></router-view>
        </div>
      </section>

      @include ('layouts.footer')
    </div>
    <script src="/js/app.js"></script>
  </body>
</html>