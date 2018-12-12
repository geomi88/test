

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>Safqa</title>
        <style type="text/css">

            p {
                margin: 0px;
            }

            img {
                outline: none;
                border: none;
            }

            .newSite, .dashBoard {
                height: 94px;
                width: 30%;
            }

            .newSite a, .dashBoard a {
                color: #fff;
                font-size: 24px;
                text-decoration: none;
                padding: 15px;
                display: block;
            }

        </style>
    </head>

    <body style="margin:0; padding:0;">
        <table cellpadding="0" cellspacing="0" border="0" align="center" style="border-top: 5px solid #680115; background: #851e32;  max-width:674px; margin: 0px; color:#454446; font-size:18px; font-family:Arial, Helvetica, sans-serif;padding-bottom: 12px;">

            

            <tr>
                <td style="padding: 0 25px;">
                    <table max-width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fff; display: block; min-height: 408px;">
                        <tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;"><p>Hi <?php echo e($user_name); ?>,</p></td>
                        </tr>
                       
			<tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;">
                            Your New Password : <?php echo e($password); ?>

                            </td>
                        </tr>
                        
                        <tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;">
                            Thank You    
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>


        </table>

    </body>
</html>

