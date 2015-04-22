<?php
/**
 * Created by PhpStorm.
 * User: akshay
 * Date: 4/22/15
 * Time: 11:26 AM
 */

global $framework;
global $app;

?>

<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/mfglabs_iconset.css">
<link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/animation/animated.min.css">
<link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/install-complete.css">
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">

<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        $framework.request({
            axn:"framework/auth",
            code:"isadmin"
        },function(r){
            console.debug(r);
            $('.server-test').removeClass('not-ok').addClass('ok');
            $('.server-test').find('.fa').removeClass('fa-spin').removeClass('fa-cog').addClass('fa-check-circle');
            $('.server-test').find('.result').html("OK");
        },function(x){
            $('.server-test').find('.fa').removeClass('fa-spin').removeClass('fa-cog').addClass('fa-exclamation-circle');
            $('.server-test').find('.result').html("NOT OK");
        });
    });
</script>


<div class="main-container container">
    <div class="row">
        <div class="container">

            <div class="grid">
                <h2 class='main-title'>Web-Service Check</h2>
                <p>Welcome to twaFramework!  This page does a quick check for web-services.</p>


                <div class="info-table">
                    <div class="info-row ok">
                        <div class="info-icon pull-right">
                            <p><i class='fa fa-check-circle'></i></p>
                        </div>
                        <div class="info-message">
                            <p><strong>Version : </strong><?php echo TWA_VERSION; ?></p>
                        </div>

                    </div>

                    <div class="info-row server-test not-ok">
                        <div class="info-icon pull-right">
                            <p><i class='fa fa-spin fa-cog'></i></p>
                        </div>
                        <div class="info-message">
                            <p><strong>Server Test : </strong><span class="result">...</span></p>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

