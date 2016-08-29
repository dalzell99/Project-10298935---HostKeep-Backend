<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Login</title>

        <?php include('head.php');
        echo '<link rel="stylesheet" type="text/css" href="css/index.css?' . filemtime('css/index.css') . '" />';
        echo '<script type="text/javascript" src="js/index.js?' . filemtime('js/index.js') . '"></script>';
        ?>
    </head>
    <body>
        <div class='.container-fluid'>
            <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                <header>
                    <?php include('header.php'); ?>
                </header>

                <main>
                    <p>
                        Welcome to HostKeep’s secure online owners portal.
                    </p>

                    <p>
                        Login here to view monthly property reports, update personal details, and view important notifications.
                    </p>

                    <p>
                        If you need further assistance please contact us on <a href='hello@hostkeep.com.au'>hello@hostkeep.com.au</a>
                    </p>

                    <table>
                        <tr>
                            <td>
                                <label for='#loginEmailInput'>Email address</label>
                            </td>
                            <td>
                                <input id='loginEmailInput' type='email' />
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for='#loginPasswordInput'>Password</label>
                            </td>
                            <td>
                                <input id='loginPasswordInput' type='password' />
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <!-- Empty -->
                            </td>
                            <td>
                                <button id='loginButton'>Submit</button>
                            </td>
                        </tr>

                        <!-- Uncomment when forgotten username page done
                        <tr>
                            <td>
                                Empty
                            </td>
                            <td>
                                <a href="./forgot-username.php">Forgot my username</a>
                            </td>
                        </tr>
                         -->

                        <tr>
                            <td>
                                <!-- Empty -->
                            </td>
                            <td>
                                <a href="./reset-password.php">Forgot my password</a>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <!-- Empty -->
                            </td>
                            <td>
                                <a href="./register.php">Register new account</a>
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
