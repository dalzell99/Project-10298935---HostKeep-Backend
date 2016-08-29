<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Register</title>

        <?php
        include('head.php');
        echo '<script type="text/javascript" src="js/register.js?' . filemtime('js/register.js') . '"></script>';
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
                        HostKeep's user dashboard is always open for you to keep your details up to date or view important documents relating to your claim. Before you can access the dashboard, you need to register your email address below. Login details will be sent to your inbox.
                    </p>

                    <table>
                        <tr>
                            <td>
                                <label for='#registerEmailInput'>Email Address</label>
                            </td>
                            <td>
                                <input id='registerEmailInput' type='email' />
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <!-- Empty -->
                            </td>
                            <td>
                                <button id='registerButton'>Register Account</button>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <!-- Empty -->
                            </td>
                            <td>
                                <a href="./index.php">Return to login</a>
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
