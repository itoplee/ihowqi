var auto = null,
	timer = null,
	carousel = new Function();
carousel.prototype = {
    init: function(container, item) {
        //默认动画频率
        this.aTime = this.aTime || 10;
        //默认间隔
        this.sTime = this.sTime || 4000;
        //图片容器
        this.item = item;
        this.container = container;
        this.oImg = this.container.getElementsByTagName("ul")[0];
        this.oImgLi = this.oImg.getElementsByTagName("li");
        //左右按钮
        // this.oL=document.getElementById('focus_l');
        // this.oR=document.getElementById('focus_r');
        //创建文字容器
        this.createTextDom();
        //默认第一帧
        this.target = 0;
        //开始动画
        this.autoMove();
        //鼠标
        this.oAction();
    },
    createTextDom: function() {
        var that = this;
        //创建文字容器
        this.oText = document.createElement("div");
        this.oText.className = "focus_s";
        var ul = document.createElement('ul');
        var frag = document.createDocumentFragment();
        for (var i = 0; i < this.oImgLi.length; i++) {
            var li = document.createElement("li");
            if (i == 0) {
                li.className = "active";
            };
            frag.appendChild(li);
        };
        ul.appendChild(frag);
        this.oText.appendChild(ul);
        this.item.appendChild(this.oText);
        //文字容器绑定动作
        this.oTextLi = this.oText.getElementsByTagName("li");
    },
    autoMove: function() {
        var that = this;
        auto = setInterval(function() { that.goNext() }, that.sTime);
    },
    goNext: function() {
        this.target = this.nowIndex();
        this.target == this.oTextLi.length - 1 ? this.target = 0 : this.target++;
        this.aStep = (this.target - this.nowIndex()) * this.step;
        this.removeClassName();
        this.oTextLi[this.target].className = "active";
        this.startMove();
    },
    goPrev: function() {
        this.target = this.nowIndex();
        this.target == 0 ? this.target = this.oTextLi.length - 1 : this.target--;
        this.aStep = (this.target - this.nowIndex()) * this.step;
        this.removeClassName();
        this.oTextLi[this.target].className = "active";
        this.startMove();
    },
    startMove: function() {
        var that = this;
        var t = 0;
        this.timer = '';

        function set() {
            if (t > 100) {
                clearInterval(that.timer);
            } else {
                for (var i = 0; i < that.oImgLi.length; i++) {
                    that.oImgLi[i].style.display = 'none';
                };
                that.oImgLi[that.target].style.display = 'block';
                that.setOpacity(that.oImg, t);
                t += 5;
            };
        };
        timer = setInterval(set, that.aTime);
    },
    setOpacity: function(elem, level) {
        if (elem.filters) {
            elem.style.filter = 'alpha(opacity=' + level + ')';
            elem.style.zoom = 1;
        } else {
            elem.style.opacity = level / 100;
        };
    },
    nowIndex: function() {
        for (var i = 0; i < this.oTextLi.length; i++) {
            if (this.oTextLi[i].className == 'active') {
                return i;
                break;
            }
        };
    },
    oAction: function() {
        var that = this;
        for (var i = 0; i < this.oTextLi.length; i++) {
            this.oTextLi[i].index = i;
            this.oTextLi[i].onclick = function() {
                clearInterval(auto);
                clearInterval(timer);
                that.setOpacity(that.oImg, 100);
                that.target = this.index;
                that.removeClassName();
                this.className = 'active';
                that.startMove();
            }
        };
        mouseEnter(that.container, 'mouseleave', function(e) {
            clearInterval(auto);
            that.autoMove();
        });
        // this.oL.onclick=function(){
        // 	that.goPrev();
        // };
        // this.oR.onclick=function(){
        // 	that.goNext();
        // };
    },
    removeClassName: function() {
        for (var i = 0; i < this.oTextLi.length; i++) {
            this.oTextLi[i].className = ""
        };
    }
};

function mouseEnter(ele, type, func) {
    if (window.document.all) {
        ele.attachEvent('on' + type, func);
    } else { //ff
        if (type === 'mouseenter') {
            ele.addEventListener('mouseover', withoutChildFunction(func), false);
        } else if (type === 'mouseleave') {
            ele.addEventListener('mouseout', withoutChildFunction(func), false);
        } else {
            ele.addEventListener(type, func, false);
        }
    };
}

function withoutChildFunction(func) {
    return function(e) {
        var parent = e.relatedTarget;
        while (parent != this && parent) {
            try {
                parent = parent.parentNode;
            } catch (e) {
                break;
            }
        }
        if (parent != this) {
            func(e);
        }
    };
}

export default carousel;
