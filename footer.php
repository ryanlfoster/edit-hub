		<div id="footer" class="do-not-print">
			<div id="footer-tag">Edit Hub is powered by <a href="http://wordpress.org/">WordPress</a>.</div>
		</div>

	</div><!--end page wrap-->

		<?php if ( is_user_logged_in() ) { ?>
			<a href="<?php echo site_url(); ?>/wp-admin/post-new.php?post_type=post" id="newpost" title="Start New Draft" >+</a>
		<?php } ?>

	<?php wp_footer(); ?>
	
	<!--[if lt IE 7]>
			
		<script>
		/* <![CDATA[ */
			sfHover = function() {
			var sfEls = jQuery('li.hub-nav');
			for (var i=0; i<sfEls.length; i++) {
				sfEls[i].onmouseover=function() {
					this.className+=" sfhover";
				}
				sfEls[i].onmouseout=function() {
					this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
				}
			}
		}
		if (window.attachEvent) window.attachEvent("onload", sfHover);
		/* ]]> */
		</script>
	
	<![endif]-->
	
</body>

</html>