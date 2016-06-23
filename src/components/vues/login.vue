<script type="text/javascript">
	export default {
		data: function () {
			return {
				type: 1,
				qquri : encodeURIComponent("http://www.itoplee.com/ihowqi/index.php/index/index/qqlogin"),
				baiduuri : encodeURIComponent("http://www.itoplee.com/ihowqi/index.php/index/index/baiduLogin")
			};
		},
		methods: {
			change: function (index) {
				this.type = index;
			}
		},
		ready: function () {
			//https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=101327498&redirect_uri=[YOUR_REDIRECT_URI]&scope=123
			//28CFA2E50896A4CE21AA84FEAC953DFB
			WB2.anyWhere(function(W){
			    W.widget.connectButton({
			        id: "wb_connect_btn",	
			        type:"3,4",
			        callback : {
			            login:function(o){	//登录后的回调函数
			            	 alert("login: " + o.screen_name);
			            },	
			            logout:function(){	//退出后的回调函数
			            	 alert('logout');
			            }
			        }
			    });
			});
		}
	};
</script>
<template>
	<div class="ihq-login">
		<div class="box">
			<div class="tab">
				<span @click="change(1)" :class="{'left': type === 2}">爱好奇登录</span>
				<span :class="{'right': type === 1}">二维码快速登陆</span>
			</div>
			<dl class="dl" v-show="type === 1">
				<dt>用户名：</dt>
				<dd><input type="text" placeholder="请输入用户名"></dd>
				<dt>密&nbsp;&nbsp;码：</dt>
				<dd><input type="password" placeholder="请输入密码"></dd>
				<dt>&nbsp;</dt>
				<dd><input type="checkbox">记住密码</dd>
			</dl>
			<div class="btn" v-show="type === 1">
				<span>登录</span>
				<a href="#">忘记密码</a>
			</div>
			<!-- <a href="#" class="register" v-show="type === 1"></a> -->
			<div class="other" v-show="type === 1">
				<label class="label">其他账号登录：</label>
				<a href="https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=101327498&redirect_uri={{qquri}}&scope=123">
					<img src="../../../public/imgs/Connect_logo_7.png" alt="qq">
				</a>
				<a href="http://openapi.baidu.com/oauth/2.0/authorize?response_type=code&client_id=e1gGLs0wZKDjqRysUHnzAGMt&redirect_uri={{baiduuri}}">
					<img src="../../../public/imgs/baidu.png" alt="baidu">
				</a>
				<div id="wb_connect_btn" style="float: left; margin-right: 5px;"></div>
			</div>
			<div class="code" id="qrcode" v-show="type === 2"></div>
		</div>
	</div>
</template>
<style lang="less">
	.ihq-login {
		position: absolute;
		top: 30px;
		right: 0;
		z-index: 5;
		width: 320px;
		height: 330px;
		border-radius: 3px;
		background-color: rgba(248,248,248,.4);

		.box {
			width: 300px;
			height: 310px;
			margin: 10px;
			border-radius: 3px;
			border: 1px solid #8a9eaa;
			background: url(../../../public/imgs/login_bg.png) 0 0 repeat-x;
		}

		.tab {
			overflow: hidden;
			span {
				float: left;
				display: inline-block;
				width: 149px;
				height: 44px;
				line-height: 44px;
				text-align: center;
				cursor: pointer;
			}
			.left {
				border: 1px solid #8a9eaa;
				background-color: #fff;
				border-top: 0;
				border-left: 0;
			}

			.right {
				border: 1px solid #8a9eaa;
				background-color: #fff;
				border-top: 0;
				border-right: 0;
			}
		}

		.dl {
			margin-top: 30px;
			overflow: hidden;
			dt {
				float: left;
				width: 70px;
				margin-top: 8px;
				text-align: right;
			}

			dd {
				margin-left: 76px;
				margin-bottom: 15px;
			}

			input {
				width: 180px;
				height: 30px;
				line-height: 30px;
				padding: 0 5px;
				border: 1px solid #ccc;
				outline: 0;
			}

			input[type="checkbox"] {
				width: 14px;
				height: 14px;
				margin-right: 5px;
			}
		}
		.btn {
			overflow: hidden;
			span {
				display: block;
			    text-indent: -999em;
			    float: left;
			    overflow: hidden;
			    height: 32px;
			    line-height: 32px;
			    overflow: hidden;
			    width: 73px;
			    height: 32px;
			    overflow: hidden;
			    background: url(../../../public/imgs/btn.png) 0 -100px no-repeat;
			    cursor: pointer;
			    margin-left: 75px;
			    margin-right: 10px;
			}
			a {
				float: left;
			    overflow: hidden;
			    height: 32px;
			    line-height: 32px;
			    overflow: hidden;
			    color: #0387cf;
			    text-decoration: none;
			}
		}

		.register {
			position: absolute;
		    width: 257px;
		    height: 40px;
		    overflow: hidden;
		    background: url(../../../public/imgs/btn.png) 0 0 no-repeat;
		    display: block;
		    text-indent: -999em;
		    cursor: pointer;
		    left: 50%;
		    margin-left: -129px;
		    top: 265px;
		}

		.other {
			margin-top: 30px;
    		margin-left: 20px;
    		overflow: hidden;

    		.label {
    			float: left;
    			margin-top: 6px;
    		}
		}

		.code {
			width: 200px;
			margin: 30px auto 0;
		}
	}
</style>