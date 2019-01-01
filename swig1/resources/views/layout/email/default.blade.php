<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" /> 
        <title>{{env('APP_NAME')}} Team </title>
        <style>
            .contentWrapper p{
                line-height: 21px  !important;
                font-size: 15px !important;
                color: #454446 !important;
            }
        </style>
    </head>

    <body style="margin:0; padding:0;">
        <table cellpadding="0" cellspacing="0" border="0"  style="border-top: 4px solid #0154a4;  background: #e4e5ec; table-layout: fixed;  max-width:674px; color:#454446; font-size:18px; font-family:Arial, Helvetica, sans-serif;">
            <tr >
                <td style="text-align: center; color: #3399ff; font-size: 24px; font-weight: normal; height: 56px;"></td>
            </tr>
            <tr>
                <td style="padding: 0 12px;  color: #454446;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fff;">
                        <tr style="text-align: center; height: 78px; background:#0154a4;">
                            <td style="border-bottom: 1px solid #e8e8e8;  padding: 22px 0;"><a href="#" title=""><img src="" width="40%" height="auto" alt=""/></a></td>
                        </tr>

                        <tr class="contentWrapper">
                            <td  style="padding:31px 20px 0 41px; margin: 0px;">
                                @yield('content')	 
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:31px 20px 0 41px; margin: 0px;"><span> Sincerely, </span></td>
                        </tr>
                        <tr>
                            <td style="padding:31px 20px 0 41px; margin: 0px;"><span>  Your Support Team  <br><b>{{env('APP_NAME')}}</b></br></span></td>
                        </tr>
                        <tr>
                            <td style="padding:31px 20px 0 41px; margin: 0px;"><span> - Innovative Networking - </span></td>
                        </tr>
                        <tr>
                            <td style="color:#747375;padding:20px 41px 0;">
                                <p style="margin: 0 0 20px;  font-size: 18px;">
                                    For any further assistance, please contact 
                                    <a style="color:red;" href=""><u> </u></a> 
                                </p></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 2px solid #e6e6e6;"></td>
                        </tr>
                    </table></td>
            </tr>
            <tr>
                <td style="padding: 14px 12px; width: 100%; color: #454446;"></td>
            </tr>
        </table>

    </body>

</html>
