<?php

session_start();
include "../db.php";

if(isset($_POST['login']))
{
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM admin
            WHERE username='$username'
            AND password='$password'";

    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0)
    {
        $_SESSION['admin'] = $username;

        header("Location: admindashboard.php");
        exit();
    }
    else
    {
        echo "<script>
        alert('Invalid Admin Login');
        </script>";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Login - EduTrack</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,sans-serif;

    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    background:
    linear-gradient(
        rgba(28,25,23,.88),
        rgba(28,25,23,.92)
    );
}

.login-box{

    width:400px;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:0 15px 40px rgba(0,0,0,.35);

}

.logo{

    text-align:center;

    font-size:30px;

    font-weight:bold;

    color:#1C1917;

    margin-bottom:10px;
}

.logo span{
    color:#FF8A00;
}

.admin-title{

    text-align:center;

    color:#1C1917;

    margin-bottom:25px;
}

.admin-title p{

    color:#78716C;

    font-size:14px;
}

input{

    width:100%;

    padding:13px;

    margin:9px 0;

    border:1px solid #D6D3D1;

    border-radius:10px;

    outline:none;

}

input:focus{

    border-color:#FF8A00;

    box-shadow:0 0 0 3px rgba(255,138,0,.12);

}

button{

    width:100%;

    padding:13px;

    margin-top:12px;

    border:none;

    border-radius:10px;

    background:linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    color:white;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;

}

button:hover{

    transform:translateY(-2px);

}

.back{

    text-align:center;

    margin-top:20px;

}

.back a{

    text-decoration:none;

    color:#FF6B00;

    font-weight:bold;

}

</style>

</head>

<body>

<div class="login-box">

    <div class="logo">
        Edu<span>Track</span>
    </div>

    <div class="admin-title">

        <h2>🔐 Admin Login</h2>

        <p>Login to access Admin Panel</p>

    </div>

    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Enter Username"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Enter Password"
            required
        >

        <button
            type="submit"
            name="login">

            Login as Admin

        </button>

    </form>

    <div class="back">

        <a href="../login.php">
            ← Student / Teacher Login
        </a>

    </div>

</div>

</body>

</html>