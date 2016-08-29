<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Change Password</title>

        <?php
        include('head.php');
        echo '<script type="text/javascript" src="js/first-time.js?' . filemtime('js/first-time.js') . '"></script>';
        ?>
    </head>
    <body>
        <div class='.container-fluid'>
            <div class="col-sm-8 col-sm-offset-2">
                <header>
                    <?php include('header.php'); ?>
                </header>

                <main>
                    <table>
                        <tr>
                            <td>
                                <label for='#firsttimeCurrentPasswordInput'>Current Password</label>
                            </td>
                            <td>
                                <input id='firsttimeCurrentPasswordInput' type='password' />
                            </td>
                            <td>
                                <i id='firsttimeCurrentPasswordCheck' class="fa fa-check fa-15x" aria-hidden="true"></i>
                                <i id='firsttimeCurrentPasswordCross' class="fa fa-times fa-15x" aria-hidden="true"></i>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for='#firsttimeNewPasswordInput'>New Password</label>
                            </td>
                            <td>
                                <input id='firsttimeNewPasswordInput' type='password' />
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for='#firsttimeConfirmPasswordInput'>Confirm Password</label>
                            </td>
                            <td>
                                <input id='firsttimeConfirmPasswordInput' type='password' />
                            </td>
                            <td>
                                <i id='firsttimeConfirmPasswordCheck' class="fa fa-check fa-15x" aria-hidden="true"></i>
                                <i id='firsttimeConfirmPasswordCross' class="fa fa-times fa-15x" aria-hidden="true"></i>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <!-- Empty -->
                            </td>
                            <td>
                                <button id='firsttimeButton'>Submit</button>
                            </td>
                        </tr>
                    </table>
                </main>

                <footer>
                    <?php include('footer.php'); ?>
                </footer>
            </div>
        </div>
    </body>
</html>
