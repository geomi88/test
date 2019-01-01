<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>MTG</title>
        <style type="text/css">

            p {
                margin: 0px;
            }

            img {
                outline: none;
                border: none;
            }

        </style>
    </head>

    <body style="margin:0; padding:0;">
        <table cellpadding="0" cellspacing="0" border="0" align="center" style="border-top: 5px solid #680115; background: #851e32;  max-width:776px; margin: 0px; color:#454446; font-size:18px; font-family:Arial, Helvetica, sans-serif;padding-bottom: 12px;">

            <tr>
                <td style="padding:32px 0; text-align: center;"><img src="{{ URL::asset('images/pageLogo.png') }}" alt="Logo" style="max-width: 84%;"/></td>
            </tr>

            <tr>
                <td style="padding: 0 25px;">
                    <table max-width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fff; display: block; min-height: 408px;">
                        <tr>
                            <td style="padding:31px 0 0 41px;"><p>Hi BARISTA <b style="color: #851e32;">{{$first_name}}</b>,</p></td>
                        </tr>
                        <tr>
                            <td style="padding:18px 41px 0 41px;font-size: 16px; line-height: 25px;">
                                <p>Congratulations! You are allocated for a new branch. Please check the new branch and other details. </p>
                                    
                                    
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;"><p>We would like you to assign for a new branch.</p></td>
                        </tr>

                        <tr>
                            <td style="padding:22px 41px 0 41px;">
                                <table max-width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%; border: 1px solid #adadad; font-size: 15px;">
                                    <tr>
                                        <td style="background: #fff0f3;padding:12px;border-right: 1px solid #adadad; border-bottom: 1px solid #adadad;">Branch Name:</td>
                                        <td style="padding:12px; border-bottom: 1px solid #adadad;">{{$branch_name}}</td>
                                    </tr>
                                    <tr>
                                        <td style="background: #fff0f3;padding:12px;border-right: 1px solid #adadad; border-bottom: 1px solid #adadad;">Shift Name:</td>
                                        <td style="padding:12px; border-bottom: 1px solid #adadad;">{{$shift_name}}</td>
                                    </tr>
                                    <tr>
                                        <td style="background: #fff0f3;padding:12px; border-right: 1px solid #adadad; border-bottom: 1px solid #adadad;">Assigned By</td>
                                        <td style="padding:12px; border-bottom: 1px solid #adadad;">{{$assigned_by}}</td>
                                    </tr>
                                    <tr>
                                        <td style="background: #fff0f3;padding:12px; border-right: 1px solid #adadad; border-bottom: 1px solid #adadad;">Assigned On</td>
                                        <td style="padding:12px; border-bottom: 1px solid #adadad;">{{$allocated_date}}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:18px 41px 26px 41px;font-size: 16px; line-height: 25px;font-style: italic;">
                                <p><a href="{{$web_url}}" style="color: #1c74ec; text-decoration: underline;">Click Here </a> and get started! With the new region login. </p>
                                <p>We wish you for a good time in this new branch & may your new environment will be more exciting and fun.  </p>
                                <p>All the very best for your success!!! </p>
                                <p>We are here to help. If you have any queries, just contact <a href="mailto:support@mtg.com" style="color: #851e32;">support@mtg.com</a></p>
                                <p style="font-style: normal;padding-top: 14px;">Thank You</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td style="padding: 0 25px;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="padding: 12px 0 0; color: #fff; text-align: center; font-size: 12px;">
                                © Moroccan Taste Group All Rights Reserved
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </body>
</html>