<!doctype html>
<html>
    <head>
        <script type="text/javascript" src="jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="jquery.pnotify.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>

        <link rel="stylesheet" href="jquery.pnotify.default.css"/>
        <link rel="stylesheet" href="jquery.pnotify.default.icons.css"/>

        <link rel="stylesheet" href="css/bootstrap-responsive.min.css"/>
        <link rel="stylesheet" href="css/bootstrap.min.css"/>

        <script type="text/javascript" src="jquery.form.js"></script>
        <script>
            $(document).ready(function() {
                $('form').ajaxForm({
                    dataType: "json",
                    beforeSend: function() {
                        return true;
                    },
                    success: function(responseText, statusText, xhr, $form) {
                        var html = "";
                        if ($("#option").val() === "gUser") {
                            html += "<table class='table'><tr><td>ID</td><td>Username</td><td>Firstname</td><td>Lastname</td><td>Email</td><td>Gender</td><td>Date of Birth</td></tr>";
                            if (!responseText.error) {
                                $.each(responseText, function(i, response) {
                                    html += "<tr><td>" + response.id + "</td><td>" + response.username + "</td><td>" + response.firstname + "</td><td>" + response.lastname + "</td><td>" + response.email + "</td><td>" + response.gender + "</td><td>" + response.dob + "</td></tr>";
                                });
                            } else {
                                $.pnotify({
                                    title: 'Error: ' + responseText.error.code,
                                    text: responseText.error.message,
                                    type: 'error',
                                    icon: 'ui-icon ui-icon-flag'
                                });
                            }
                            $("#result").html(html + "</table>");
                        } else if ($("#option").val() === "regStat") {
                            html += "<p><strong>Regiter Today:</strong>"+responseText.regToday+"</p><p><strong>Total Regiter:</strong>"+responseText.totalReg+"</p><p>Last Ten Members</p><table class='table'><tr><td>ID</td><td>pix</td><td>Username</td><td>Firstname</td><td>Lastname</td><td>Email</td><td>Gender</td><td>Date of Birth</td><td>Date Joined</td><td>Activated</td></tr>";
                            if (!responseText.error) {
                                $.each(responseText.lastTen, function(i, response) {
                                    html += "<tr><td>" + response.id + "</td><td><img src='http://www.gossout.com/" + (response.photo.nophoto?response.photo.alt:response.photo.thumbnail150) + "'/></td><td>" + response.username + "</td><td>" + response.firstname + "</td><td>" + response.lastname + "</td><td>" + response.email + "</td><td>" + response.gender + "</td><td>" + response.dob + "</td><td>" + response.dateJoined + "</td><td>" + response.activated + "</td></tr>";
                                });
                            } else {
                                $.pnotify({
                                    title: 'Error: ' + responseText.error.code,
                                    text: responseText.error.message,
                                    type: 'error',
                                    icon: 'ui-icon ui-icon-flag'
                                });
                            }
                            $("#result").html(html + "</table>");
                        }
                    },
                    complete: function(xhr, textStatus) {

                    }
                });

            });
        </script>
        <style>
            .container{
                margin: 0 auto;
                width: 80%;
                margin-top: 25px;
            }
            ul{
                list-style: none;
            }
            li:hover{
                background: #bbb;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <form method="POST" action="run.php">
                <ul>
                    <li>VALUE: <input type="text" name="input" class="input-large"/></li>
                    <li>DECODE VALUE: <input type="checkbox" name="decode" value="TRUE" class="checkbox"/></li>
                    <li>OPTION: 
                        <select name="option" id="option">
                            <option value="gUser">Get User Info</option>
                            <option value="regStat">Get Registration statistics</option>
                        </select>
                    </li>
                    <li><input type="submit" class="btn" /></li>
                </ul>
            </form>
            <span id="result"></span>
        </div>

    </body>
</html>