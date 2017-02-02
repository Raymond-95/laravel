<html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function (){



        $.ajax({
            url: 'http://www.jpj.gov.my/web/guest/tarikh-luput-lesen-memandu',  //Pass URL here 
            type: "GET", //Also use GET method
            success: function(data) {
                var time = $(data).find('.captcha').html();
                alert(time);
            }
        });


});

</script>
    <head>
        <title>Laravel</title>

        <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>

        <script type="text/javascript">
            
           var a="http://www.jpj.gov.my/web/guest/tarikh-luput-lesen-memandu?p_p_id=jpjdrivinglicense_WAR_jpjallportlet_INSTANCE_Ux42&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=getDataResourceURL&p_p_cacheability=cacheLevelPage&p_p_col_id=column-2&p_p_col_count=5&_jpjdrivinglicense_WAR_jpjallportlet_INSTANCE_Ux42_jspPage=%2Fview.jsp";


           $.post(a,{action:"check",idNum:"950703025239",categoryId:"1"}}

        </script>
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
