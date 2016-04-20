

</section>

<!-- LIENS PIED DE PAGE -->
<footer>
    <div class="navbar-footer navbar-inverse" role="navigation">
        <ul>
            <li><a href="<?php echo RACINE_SITE; ?>mentions.php">Mentions légales - </a></li>
            <li><a href="<?php echo RACINE_SITE; ?>cgv.php">C.G.V. - </a></li>
            <li><a href="<?php echo RACINE_SITE; ?>plan_site.php">Plan du site - </a></li>
            <li><a href="<?php echo RACINE_SITE; ?>contact.php">Contact</a></li>
        </ul>
    </div>
</footer>

<!-- JAVASCRIPTS NECESSAIRES -->
<script src="<?php echo RACINE_SITE; ?>js/jquery-2.2.0.js"></script>
<script src="<?php echo RACINE_SITE; ?>js/bootstrap.min.js"></script>
<script src="<?php echo RACINE_SITE; ?>js/bootstrap-hover-dropdown.js"></script>
<script src="<?php echo RACINE_SITE; ?>js/jquery-ui/jquery-ui.js"></script>
<script>
    $(function()
    {
        $('.carousel').carousel({
            interval: 3000
        });
        $('blockquote a').tooltip();
        $('.btn-group .btn:nth-child(1)').click(function ()
        {
            $('iframe').attr('src', 'http://www.youtube.com/embed/VmnIeLmjuHA');
        })
        $('.btn-group .btn:nth-child(2)').click(function ()
        {
            $('iframe').attr('src', 'http://www.youtube.com/embed/fRAG4T-iqqY');
        })
        $('.btn-group .btn:nth-child(3)').click(function ()
        {
            $('iframe').attr('src', 'http://www.youtube.com/embed/yua4jzecCIE');
        })
    });
</script>

</div>
</body>
</html>
