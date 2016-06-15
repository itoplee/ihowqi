<script type="text/javascript">
	import focus from "../services/carousel";
	import login from "./login";

	export default {
		components: {login},
		ready: function () {
			 new focus().init(document.getElementById("ihq-banner"), document.getElementById("ihq-context"));
		}
	};
</script>
<template>
	<div class="ihq-banner focus_m" id="ihq-banner">
		<div class="context" id="ihq-context">
			<login></login>
		</div>
	    <ul class="ul">
			<li class="li li_1"></li>
			<li class="li li_2"></li>
			<li class="li li_3"></li>
		</ul>
	</div>
</template>
<style lang="less">
	.ihq-banner {position: relative; height: 390px; margin-top: 60px;
		.context {position: relative; width: 1000px; height: 390px; margin: 0 auto;}
		.ul {
			position: absolute; 
			top: 0; 
			left: 0; 
			z-index: -1;
			list-style: none; 
			width: 100%; 
			height: 390px; 
			overflow: hidden;
		}
		.li {width: 100%; height: 390px;}
		.li_1 {background:url(../../../public/imgs/1.jpg) center 0 no-repeat rgb(193, 49, 40);}
		.li_2 {background:url(../../../public/imgs/2.jpg) center 0 no-repeat #76b7d8;}
		.li_3 {background:url(../../../public/imgs/3.jpg) center 0 no-repeat #288cc0;}
		.focus_s {position: absolute; top: 368px; left: 310px; z-index: 5;
			ul {overflow: hidden;}
			li {
				float: left;
			    width: 10px;
			    height: 10px;
			    margin-right: 10px;
			    list-style: none;
			    border: 0;
			    border-radius: 50%;
			    cursor: pointer;
			    background-color: rgba(255,255,255,.3);				
			}
			.active {background-color: rgba(255,255,255,1);}
		}
	}
</style>