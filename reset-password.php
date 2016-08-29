<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Reset Password</title>

        <?php include('head.php');
        echo '<link rel="stylesheet" type="text/css" href="css/reset-password.css?' . filemtime('css/reset-password.css') . '" />';
        echo '<script type="text/javascript" src="js/reset-password.js?' . filemtime('js/reset-password.js') . '"></script>';
        ?>
    </head>
    <body>
        <div class='.container-fluid'>
            <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                <header>
                    <?php include('header.php'); ?>
                </header>

                <main>
                    <div id='resetpasswordGetTempPassword'>
                        <p>
                            If you have forgotten your password, enter your email address below and a new temporary password will be sent to you inbox immediately. You will be asked to change this temporary password after login.
                        </p>

                        <table>
                            <tr>
                                <td>
                                    <label for'#resetpasswordEmailInput'>Email address</label>
                                </td>
                                <td>
                                    <input id='resetpasswordEmailInput' type='email' />
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <!-- Empty -->
                                </td>
                                <td>
                                    <button id='resetpasswordButton'>Reset Password</button>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <!-- Empty -->
                                </td>
                                <td>
                                    <a href='/index.php'>Return to login</a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id='resetpasswordChangePassword'>
                        <p>
                            Set your new password below.
                        </p>

                        <table>
                            <tr>
                                <td>
                                    <label for'#resetpasswordNewPasswordInput'>New Password</label>
                                </td>
                                <td>
                                    <input id='resetpasswordNewPasswordInput' type='password' />
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label for'#resetpasswordConfirmPasswordInput'>Confirm Password</label>
                                </td>
                                <td>
                                    <input id='resetpasswordConfirmPasswordInput' type='password' />
                                </td>
                                <td>
                                    <i id='resetpasswordConfirmPasswordCheck' class="fa fa-check fa-15x" aria-hidden="true"></i>
                                    <i id='resetpasswordConfirmPasswordCross' class="fa fa-times fa-15x" aria-hidden="true"></i>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <!-- Empty -->
                                </td>
                                <td>
                                    <button id='resetpasswordChangePasswordButton'>Change Password</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </main>

                <footer>
                    <?php include('footer.php'); ?>
                </footer>
            </div>
        </div>
    </body>
</html>
