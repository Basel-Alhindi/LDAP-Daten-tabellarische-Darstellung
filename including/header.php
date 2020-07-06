<div class="header">
    <form action="" method="post" target="_self">
        <input id="logout" type="submit" name="logout" value="Logout" class="ad-right" >
        <?php if (isset($_POST["logout"])) {
            session_destroy();
            header("location: index.php");
        }elseif (isset($_POST["synchr"])){
            header("location: datenbank_einfuegen.php");
        }
        ?>
        <input class="ad-right" TYPE="submit" name="synchr" VALUE="Synchr.">
        <input class="ad-right" TYPE="submit" VALUE="Back" onclick="history.go(-1)" >
        <a href="tools_liste.php" alt="Home">
            <img src="img/logo.png" alt="Logo" title="Startseite" />
        </a>
    </form>


</div>