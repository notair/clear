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
<div id="blog" class="site-content">
	<div class="random">
	<?php
	global $post;
	$postid = $post->ID;
	$args = array( ‘orderby’ => ‘rand’, ‘post__not_in’ => array($post->ID), ‘showposts’ =>2);
	//query_posts("orderby=rand&order=asc&limit=1&showposts=3")
	$query_posts = new WP_Query();
	//$query_posts->query($args);
	$args=$query_posts->query("orderby=rand&order=asc&limit=1&showposts=3");
	?>
	<?php while ($query_posts->have_posts()) : $query_posts->the_post(); ?>
	<div class="random-img"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" target="_blank"><img src="<?php echo catch_first_image();?>" ></a></div>
	<?php endwhile; ?>
	</div>

			<?php  
				if(have_posts()) :
			?>
			<ul id="timeline" class="clearfix">
				<li>
					<article <?php post_class( 'clearfix' ); ?>>
						<header class="entry-header">
							<!-- END .entry-meta 
							<?php if(has_post_thumbnail()) : ?>
							<div class="entry-featured">
								<figure class="entry-thumnail">
									<?php the_post_thumbnail( 'post-thumb' ); ?>
								</figure>
							</div>			
							<?php endif; ?>
							END .entry-featured -->

							<h1 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" target="_blank"><?php the_title(); ?></a></h1>

							<div class="entry-meta">
								<span class="entry-date">
									<?php echo twenty_posted_on(); ?>
									<?php if(function_exists('the_views')) { the_views(); } ?>
								</span>
							</div>

						</header>

				<div id="preview">
				<?php if (get_first_image()) {?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" target="_blank"><img src="<?php echo get_first_image(); ?>" alt="<?php the_title(); ?>" ></a>
				<?php } ?>
				</div>

						<!-- END .entry-header -->
						<div class="entry-summary">	<?php the_excerpt(); ?>
							<a href="<?php the_permalink();; ?>" textalign="right" target="_blank"  style="font-weight: 700;">>>>查看全文</a>
						</div>

						<!-- END .entry-summanry -->
					</article>
				</li>
			</ul>
			<div class="infinite-loader"></div>
			<div class="pagination clearfix">
				<?php twenty_paging_nav(); ?>
			</div>
			<?php else: 
					get_template_part( 'content', 'none' );
				endif;
			?>
		</main>
		<!-- END site-main -->
	</div>
	<!-- END #primary -->

</div>
<!-- END .site-content -->
	</article>

	<aside>
	  <h4>Epcot Center</h4>
	  <p>The Epcot Center is a theme park in Disney World, Florida.</p>
	</aside>

	<footer>
	</footer>
</body>
</html>