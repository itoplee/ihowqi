<script type="text/javascript">
	export default {

	};
</script>
<template>
	<div class="ihq-header">
		<div class="context">
			<img class="logo" src="../../../public/imgs/logo.png">
			<ul class="nav">
				<li class="active"><a class="index" href="/ihowqi/">首页</a></li>
				<li><a class="survey" href="#">帖子</a></li>
				<li><a class="prize" href="#">话题</a></li>
				<li><a class="thread" href="#">常见问题</a></li>
			</ul>
		</div>
	</div>
</template>
<style lang="less">
	.ihq-header {
		position: fixed;
		top: 0;
		left: 0;
		z-index: 50;
		width: 100%;
		height: 60px;
		border-bottom: 1px solid #ccc;
		background-color: #fff;
		-moz-box-shadow: 0 0 3px rgba(0, 0, 0, 0.10);
    	-webkit-box-shadow: 0 0 3px rgba(0, 0, 0, 0.10);
		box-shadow: 0 0 3px rgba(0, 0, 0, 0.10);
		.context {
			width: 1000px;
			margin: 0 auto;
			overflow: hidden;

			.logo {
				float: left;
				height: 54px;
				margin-top: 3px;
			}

			.nav {list-style: none; display: block;float: right;position: relative;margin:0;}
			.nav > li {float:left;margin:15px 0 0 30px;}
			.nav > li a{display: inline-block; font-size:16px;color:#2A2A2A;height:35px;line-height:35px;padding:0 5px; text-decoration: none;}
			.nav > li.active a, .nav > li a:hover {border-bottom:2px solid;}
			.nav > li.active a.index, .nav > li a.index:hover {color:#FF6600;}
			.nav > li.active a.survey, .nav > li a.survey:hover {color:#5FB800;}
			.nav > li.active a.prize, .nav > li a.prize:hover {color:#6C58C0;}
			.nav > li.active a.down, .nav > li a.down:hover {color:#0093A8;}
			.nav > li.active a.thread, .nav > li a.thread:hover {color:#CE4B27;}
		}
	}
</style>