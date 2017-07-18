<html>
    <head>
        <link rel="stylesheet" href="{{asset('css/reset.css')}}" type="text/css">
    </head>

    <body>
        <div id="app"></div>
        <div class="container">
            <div class="content">
                <div class="title">
                    <p>@{{ message }}</p>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="{{asset('js/vue.js')}}"></script>
    <script type="text/javascript">
        new Vue({
            el: '.title',
            data: {
                message: 'Hello Laravel!'
            }
        })
    </script>
</html>