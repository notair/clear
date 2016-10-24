<?php
/**
 * The Header for our theme. *
 * Displays all of the <head> section and everything up till <div id="content"> *
 * @package clear
 */
?>
<!DOCTYPE HTML>
<html>
<head>
	<?php if (is_home()): ?>
	<title><?php bloginfo('name'); ?> </title>
	<?php else: ?>
	<title><?php wp_title('',true,''); ?></title>
	<?php endif; ?> 
	<?php wp_head(); ?>
</head>

<body>
<header id="masthead" class="site-header" role="banner">
		<div class="search-area"> 
			<form class="search" action="<?php echo home_url('/'); ?>">
                <input type="text" class="s" name="s" placeholder="search..">
                <input type="submit" class="submit" value="">
                <i class="fa fa-search"></i>
			</form>
		</div>
		<!-- END .search-area -->

		<div class="site-branding">			
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>	
		</div>

		<div id="nav">
			<ul >
			<?php //wp_list_pages('title_li='); 
				//wp_list_categories('orderby=ID&order=ASC&depth=1&hide_empty=0&title_li=');	
				// 将.......替换成你原来的参数
			    $variable = wp_list_categories('orderby=ID&order=ASC&depth=1&hide_empty=0&title_li=&echo=0');
			    // 正则替换掉title
			    echo preg_replace('/title=\"(.*?)\"/','',$variable);
				// 正则替换掉title
    			//echo preg_replace('/title=\"(.*?)\"/','',$variable);
				?>
			</ul>				
		</div>	
		<!-- END .main-navigation -->
	</header>

	<article>
	  <h1>Internet Explorer 9</h1>
	  <p> Windows Internet Explorer 9(缩写为 IE9 )在2011年3月14日21:00 发布。</p>
	</article>

	<aside>
	  <h4>Epcot Center</h4>
	  <p>The Epcot Center is a theme park in Disney World, Florida.</p>
	</aside>

	<footer>
	</footer>
</body>
</html>