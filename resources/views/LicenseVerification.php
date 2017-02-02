<html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function (){

        $.ajax({
            url: 'http://www.jpj.gov.my/web/guest/tarikh-luput-lesen-memandu',
            jsonpCallback: 'callbackFnc',
            type: 'GET',
            async: false,
            crossDomain: true,
            success: function(data) {
                var time = $(data).find('.captcha').html();
                alert(time);
            }
        });
    });

</script>
    <head>
        Laravel 5
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Laravel 5</div>
                <div class="quote">{{ Inspiring::quote() }}</div>
            </div>
        </div>
    </body>
</html>
