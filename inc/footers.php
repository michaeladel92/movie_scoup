
    <!-- JavaScript -->
    <script src="js/xml.js"></script>
    <script src="js/app.js"></script>
    <?php if(strtolower(basename($_SERVER['SCRIPT_NAME'])) == strtolower('Login_reg.Php')):?>
    <script src="js/login_reg.js"></script>
    <?php elseif(strtolower(basename($_SERVER['SCRIPT_NAME'])) == strtolower('dashboard.Php')):?>
    <script src="js/dashboard.js"></script>
    <?php elseif(strtolower(basename($_SERVER['SCRIPT_NAME'])) == strtolower('profile.Php')):?>
    <script src="js/profile.js"></script>
    <?php elseif(strtolower(basename($_SERVER['SCRIPT_NAME'])) == strtolower('index.Php')):?>
    <script src="js/home.js"></script>
    <?php endif;?>
</body>
</html>

<?php
ob_end_flush();
//close connection
$con = null;