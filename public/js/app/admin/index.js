// ========================分页=============================================
var paginate = {
    template: '#paginate',
    props: ['row', 'total', 'index'],
    data: function() {
        return {
            page: 0,
            dis: 5,
            items: []
        };
    },
    methods: {
        first: function() {
            this.index = 0;
            this.$parent.getList(this.index);
        },
        prev: function() {
            if (this.index > 0) {
                this.index = this.index - 1;
            }
            this.$parent.getList(this.index);
        },
        next: function() {
            if (this.index < (this.page - 1)) {
                this.index = this.index + 1;
            }
            this.$parent.getList(this.index);
        },
        last: function() {
            this.index = this.page - 1;
            this.$parent.getList(this.index);
        },
        change: function(index) {
            this.index = index - 1;
            this.$parent.getList(this.index);
        }
    },
    watch: {
        total: function() {
            var group = parseInt(this.index / this.dis, 10),
                start = group * this.dis + 1,
                end = start + this.dis;
            this.page = parseInt((this.total - 1) / this.row, 10) + 1;
            this.items = [];
            if (end > this.page) {
                end = this.page + 1;
            }
            for (var i = start; i < end; i++) {
                this.items.push(i);
            }

        }
    },
    events: {
        reset: function () {
            this.first();
        }
    }
};
// ========================项目报表（所有项目）=============================================
var projectChart = {
    template: '#project-chart',
    data: function() {
        return {
            obj: {}
        };
    },
    methods: {
        add: function() {
            this.$parent.next("", "projectAdd");
        },
        getData: function() {
            var _this = this;
            myPost('../get/getProjectsChart', {}, function(json) {
                if (json.code === 200) {
                    _this.obj = json.data;
                }
            });
        }
    },
    ready: function() {
        this.getData();
    }
};

// ========================未归档项目列表=============================================
var projectList = {
    template: '#project-list',
    data: function() {
        return {
            list: [],
            showModal: false,
            projectName: "",
            companys: [],
            link: "",
            project: {
                projectId: 0,
                companyId: 0,
                uid: "",
                companyParam: "",
                companyCompete: "",
                companyRefuse: "",
                companyFull: ""
            },
            pageRowCount: 12,
            total: 0,
            state: -1,
            keyword: ""
        };
    },
    methods: {
        search: function() {
            this.getList(0);
        },
        getList: function(index) {
            var _this = this,
                param = { page: index, rowCount: _this.pageRowCount };
            if (parseInt(this.state, 10) >= 0) {
                param.state = parseInt(this.state, 10) + 1;
            }
            if (this.keyword.length > 0) {
                param.name = this.keyword;
            }
            myPost('../get/getProjects', param, function(json) {
                if (json.code === 200) {
                    _this.list = json.data.list;
                    _this.total = json.data.rowCount;
                }
            });
        },
        changeState: function(p) {
            var _this = this;
            myPost('../save/projectHang', { 'id': p.id, 'flag': p.state }, function(json) {
                if (json.code === 200) {
                    if(parseInt(p.state, 10) === 5){
                        _this.getList(0);
                    }
                } else {
                    alert(json.error);
                }
            });
        },
        change: function(id) {
            this.$parent.next(id, "projectAdd");
        },
        outsource: function(id) {
            this.$parent.next(id, "projectOutsource");
        },
        process: function(id) {
            this.$parent.next(id, "projectProcess");
        },
        links: function (id) {
            this.$parent.next(id, "projectLink");
        },
        delete: function(id, flag) {
            var _this = this;
            if (flag) {
                return;
            }
            if (confirm("确定要删除吗？")) {
                myPost('../delete/deleteProject', { 'id': id }, function(json) {
                    if (json.code === 200) {
                        _this.getList(0);
                    } else {
                        alert(json.error);
                    }
                });
            }
        }
    },
    components: {
        "paginate": paginate
    },
    ready: function() {
        this.getList(0);
    }
};

// ========================已归档项目=============================================
var projectListFinish = {
    template: '#project-list-finish',
    data: function() {
        return {
            list: [],
            pageRowCount: 12,
            total: 0,
            keyword: ""
        };
    },
    methods: {
        getList: function(index) {
            var _this = this,
                param = { page: index, rowCount: _this.pageRowCount };
            if (this.keyword.length > 0) {
                param.name = this.keyword;
            }
            myPost('../get/getFinishedProjects', param, function(json) {
                if (json.code === 200) {
                    _this.list = json.data.list;
                    _this.total = json.data.rowCount;
                }
            });
        },
        search: function() {
            this.getList(0);
        },
        chart: function(id) {
            this.$parent.next(id, "projectChartOne");
        }
    },
    components: { "paginate": paginate },
    ready: function() {
        this.getList(0);
    }
};

// ========================添加/修改项目=============================================
var projectAdd = {
    template: '#project-add',
    props: ['projectId'],
    data: function() {
        return {
            text: "添加项目",
            types: [],
            project: {
                id: this.$parent.projectId,
                name: "",
                link: "",
                start: "",
                end: "",
                typeId: 1,
                time: 0,
                compete: "",
                refuse: "",
                full: "",
                amount: 0,
                pid: 0,
                muiltyLink: false,
                fileName: ""
            },
            linkCount: 0
        };
    },
    methods: {
        back: function() {
            this.$parent.back();
        },
        add: function() {
            var _this = this;
            this.project.isMuilty = 0;
            if (this.project.muiltyLink) {
                this.project.isMuilty = 1;
                this.project.link = "";
            }
            myPost("../save/addProject", this.project, function(json) {
                if (json.code === 200) {
                    _this.back();
                } else {
                    alert(json.error);
                }
            });
        },
        getProjectInfo: function() {
            var _this = this;
            myPost('../get/getProjectInfo', { id: this.project.id }, function(json) {
                if (json.code === 200) {
                    if (json.data && json.data.length > 0) {
                        _this.text = "修改项目";
                        _this.project.name = json.data[0].name;
                        _this.project.link = json.data[0].link;
                        _this.project.start = json.data[0].start_time;
                        _this.project.end = json.data[0].end_time;
                        _this.project.time = json.data[0].use_time;
                        _this.project.typeId = json.data[0].type_id;
                        _this.project.compete = json.data[0].link_compete;
                        _this.project.refuse = json.data[0].link_refuse;
                        _this.project.full = json.data[0].link_full;
                        _this.project.amount = json.data[0].amount;
                        _this.project.pid = 1;
                        _this.project.muiltyLink = json.data[0].is_muilty_link > 0 ? true : false
                    }
                }
            });
        }
    },
    ready: function() {
        var _this = this;
        myPost("../get/getProjectTypes", {}, function(json) {
            if (json.code === 200) {
                _this.types = json.data;
            }
        });
        myPost("../get/getUUID", {}, function(json) {
            if (json.code === 200) {
                if (_this.project.id.length > 0) {
                    _this.getProjectInfo();
                } else {
                    _this.project.id = json.data.uuid;
                    _this.project.compete = _this.$parent.base + 'project/2/' + _this.project.id + '/';
                    _this.project.refuse = _this.$parent.base + 'project/3/' + _this.project.id + '/';
                    _this.project.full = _this.$parent.base + 'project/4/' + _this.project.id + '/'
                }
            }
        });

        $("#file1").bind("change", function() {
            $(this).unbind("change");
            $("#file2").trigger("click");
        });

        $("#file2").bind("click", function() {
            $.ajaxFileUpload({
                url: '../upload/upload', //用于文件上传的服务器端请求地址
                secureuri: false, //是否需要安全协议，一般设置为false
                fileElementId: 'file1', //文件上传域的ID
                dataType: 'json', //返回值类型 一般设置为json
                success: function(data, status) { //服务器成功响应处理函数
                    if (data.code === 200) {
                        _this.project.fileName = data.name;
                        _this.linkCount = data.len;
                    } else {
                        alert(data.error);
                    }

                    $("#file1").bind("change", function() {
                        $(this).unbind("change");
                        $("#file2").trigger("click");
                    });
                },
                error: function(data, status, e) { //服务器响应失败处理函数
                    alert(e);
                    $("#file1").bind("change", function() {
                        $(this).unbind("change");
                        $("#file2").trigger("click");
                    });
                }
            });
            return false;
        });
    }
};

// ========================项目外包=============================================
var projectOutsource = {
    template: '#project-outsource',
    data: function() {
        return {
            companys: [],
            outsource: {
                id: "",
                projectId: this.$parent.projectId,
                companyId: 0,
                compete: "",
                refuse: "",
                full: "",
                amount: 0
            }
        };
    },
    methods: {
        back: function() {
            this.$parent.back();
        },
        getCompanys: function() {
            var _this = this;
            myPost('../get/getCompanys', {}, function(json) {
                if (json.code === 200) {
                    _this.companys = json.data;
                    if (json.data && json.data.length > 0) {
                        _this.outsource.companyId = json.data[0].id;
                    }
                }

                _this.getProjectCompany();
            });
        },
        getProjectCompany: function() {
            var _this = this;
            myPost('../get/getProjectCompany', {
                'projectId': this.outsource.projectId,
                'companyId': this.outsource.companyId
            }, function(json) {
                if (json.code === 200) {
                    if (json.data) {
                        _this.outsource.id = json.data.id;
                        _this.outsource.amount = json.data.amount;
                        _this.outsource.compete = json.data.link_compete;
                        _this.outsource.refuse = json.data.link_refuse;
                        _this.outsource.full = json.data.link_full;
                    } else {
                        _this.outsource.id = "";
                        _this.setLocalCompany();
                    }
                }
            });
        },
        setLocalCompany: function() {
            var _this = this;
            this.companys.map(function(company) {
                if (parseInt(company.id, 10) === parseInt(_this.outsource.companyId, 10)) {
                    _this.outsource.compete = company.link_compete;
                    _this.outsource.refuse = company.link_refuse;
                    _this.outsource.full = company.link_full;
                    _this.outsource.amount = 0;
                }
            })
        },
        addProjectCompany: function() {
            var _this = this;
            myPost('../save/addProjectCompany', this.outsource, function(json) {
                if (json.code === 200) {
                    _this.back();
                } else {
                    alert(json.error);
                }
            });
        }
    },
    watch: {
        'outsource.companyId': function() {
            this.setLocalCompany();
        }
    },
    ready: function() {
        this.getCompanys();
    }
};

// ========================项目进度=============================================
var projectProcess = {
    template: '#project-process',
    data: function() {
        return {
            list: [],
            innerLink: "",
            outLinks: [],
            project: {},
            linkCount: 0,
            info: {
                id: "",
                name: "整体进度",
                amount: 0,
                partIn: 0,
                compete: 0,
                refuse: 0,
                full: 0,
                unfinish: 0,
                min: 0,
                max: 0,
                avg: 0,
                domId: "chart_t"
            }
        };
    },
    methods: {
        back: function() {
            this.$parent.back();
        },
        refush: function() {
            this.outLinks = [];
            this.info = {
                id: "",
                name: "整体进度",
                amount: 0,
                partIn: 0,
                compete: 0,
                refuse: 0,
                full: 0,
                unfinish: 0,
                min: 0,
                max: 0,
                avg: 0,
                domId: "chart_t"
            };
            this.getData();
        },
        getData: function() {
            var _this = this;
            myPost('../get/getProjectProcess', { 'id': this.$parent.projectId }, function(json) {
                if (json.code === 200) {
                    var sum = 0,
                        total = 0,
                        arr = [],
                        innerObj = null;

                    json.data.list.map(function(c) {
                        c.domId = "chart_" + c.id;
                        c.min = parseInt(c.min, 10)
                        c.max = parseInt(c.max, 10)
                        _this.info.amount += parseInt(c.amount, 10);
                        _this.info.partIn += parseInt(c.partIn, 10);
                        _this.info.compete += parseInt(c.compete, 10);
                        _this.info.refuse += parseInt(c.refuse, 10);
                        _this.info.full += parseInt(c.full, 10);
                        _this.info.unfinish += parseInt(c.unfinish, 10);
                        _this.info.min = c.min < _this.info.min ? c.min : _this.info.min;
                        _this.info.max = c.max > _this.info.max ? c.max : _this.info.max;
                        sum += parseInt(c.sum, 10);
                        total += parseInt(c.total, 10);

                        if (c.isOut) {
                            arr.push(c);
                            _this.outLinks.push({
                                name: c.name,
                                link: _this.$parent.base + "outlink/" + c.id + "/会员id"
                            });
                        } else {
                            innerObj = c;
                        }
                    });

                    _this.info.avg = getFixed(sum, total, 1, 2);
                    if (innerObj) {
                        arr.unshift(innerObj);
                    }
                    arr.unshift(_this.info);
                    _this.list = arr;
                    _this.project = json.data.project;
                    _this.linkCount = json.data.linkCount;

                    setTimeout(function() {
                        _this.list.map(function(c) {
                            chartCategory(c.domId, c);
                        });
                    }, 200);
                }
            });
        }
    },
    ready: function() {
        this.getData();
        this.innerLink = this.$parent.base + "innerlink/" + this.$parent.projectId + "/会员id";
    }
};

// ========================项目报表（单个项目）=============================================
var projectChartOne = {
    template: '#project-chart-one',
    methods: {
        back: function() {
            this.$parent.back();
        }
    }
};

// ========================项目类型=============================================
var projectType = {
    template: '#project-type',
    data: function() {
        return {
            list: [],
            keyword: "",
            name: "",
            typeId: 0,
            typeName: "",
            scoreList: [],
            showScore: false,
            showAdd: false,
            min: 0,
            max: 0,
            score: 0
        };
    },
    methods: {
        search: function() {
            var _this = this,
                url = "../get/getProjectTypes",
                param = {};
            if (this.keyword.length > 0) {
                param = { 'name': this.keyword };
            }
            myPost(url, param, function(json) {
                if (json.code === 200) {
                    _this.list = json.data;
                }
            });
            this.showScore = false;
        },
        addType: function() {
            if (this.name.length > 0) {
                var _this = this;
                myPost('../save/addProjectType', { 'name': this.name }, function(json) {
                    if (json.code === 200) {
                        _this.name = "";
                        _this.search();
                    } else {
                        alert(json.error);
                    }
                });
            } else {
                alert("请输入项目类型名称");
            }
        },

        updateType: function(id, text) {
            var _this = this;
            if (text.length < 1) {
                return;
            }

            myPost('../save/updateProjectType', {
                'id': id,
                'name': text
            }, function(json) {
                if (json.code === 200) {
                    _this.updateProjectType(json.data[0]);
                } else {
                    alert(json.error);
                }
            });
        },

        deleteType: function(id) {
            var _this = this;
            myPost('../delete/deleteProjectType', { 'id': id }, function(json) {
                if (json.code === 200) {
                    _this.search();
                } else {
                    alert(json.error);
                }
            })
        },
        updateProjectType: function(c) {
            if (c) {
                this.list.map(function(item) {
                    if (item.id === c.id) {
                        item.name = c.name;
                    }
                });
            }
        },
        chooseType: function(typeId, typeName) {
            this.typeId = typeId;
            this.typeName = typeName;
            this.searchScore();
            this.showScore = true;
        },
        searchScore: function() {
            var _this = this;
            myPost('../get/getProjectTypeScore', { 'typeId': this.typeId }, function(json) {
                if (json.code === 200) {
                    _this.scoreList = json.data;
                }
            });
        },
        addScore: function() {
            var _this = this;
            myPost('../save/addProjectTypeScore', {
                'typeId': this.typeId,
                'min': this.min,
                'max': this.max,
                'score': this.score
            }, function(json) {
                if (json.code === 200) {
                    _this.showAdd = false;
                    _this.searchScore();
                    _this.min = 0;
                    _this.max = 0;
                    _this.score = 0;
                }
            });
        },
        updateScore: function(t) {
            var _this = this;
            myPost('../save/updateProjectTypeScore', {
                'id': t.id,
                'typeId': t.type_id,
                'min': t.min_time,
                'max': t.max_time,
                'score': t.score
            }, function(json) {
                if (json.code === 200) {} else {
                    alert(json.error)
                }
            });
        },
        deleteScore: function(id) {
            var _this = this;
            myPost('../delete/deleteProjectTypeScore', { 'id': id }, function(json) {
                if (json.code === 200) {
                    _this.searchScore();
                }
            });
        }
    },
    ready: function() {
        this.search();
    }
};

// ========================外包公司=============================================
var projectCompany = {
    template: '#project-company',
    data: function() {
        return {
            list: [],
            keyword: "",
            name: ""
        };
    },
    methods: {
        search: function() {
            var _this = this,
                url = "../get/getCompanys",
                param = {};
            if (this.keyword.length > 0) {
                param = { 'name': this.keyword };
            }
            myPost(url, param, function(json) {
                if (json.code === 200) {
                    _this.list = json.data;
                }
            });
        },
        add: function() {
            if (this.name.length > 0) {
                var _this = this;
                myPost('../save/addCompany', { 'name': this.name }, function(json) {
                    if (json.code === 200) {
                        _this.name = "";
                        _this.search();
                    } else {
                        alert(json.error);
                    }
                });
            } else {
                alert("请输入公司名称");
            }
        },
        edit: function(c) {
            var _this = this;
            myPost('../save/updateCompany', {
                'id': c.id,
                'name': c.name,
                'compete': c.link_compete,
                'refuse': c.link_refuse,
                'full': c.link_full
            }, function(json) {
                if (json.code === 200) {} else {
                    alert(json.error);
                }
            });
        },
        delete: function(id) {
            var _this = this;
            myPost('../delete/deleteCompany', { 'id': id }, function(json) {
                if (json.code === 200) {
                    _this.search();
                } else {
                    alert(json.error);
                }
            })
        }
    },
    ready: function() {
        this.search();
    }
};

// ========================问卷地址=============================================
var projectLink = {
    template: '#project-link',
    data: function() {
        return {
            list: [],
            keyword: "",
            name: "",
            versions: [],
            version: 1,
            pageRowCount: 10,
            total: 0
        };
    },
    methods: {
        back: function() {
            this.$parent.back();
        },
        changeVersion: function () {
            this.$broadcast('reset');
            this.getList(0);
        },
        getList: function(index) {
            var _this = this,
                param = {'id': this.$parent.projectId, page: index, rowCount: _this.pageRowCount};
            if(parseInt(this.version, 10) > 0){
                param.version = parseInt(this.version, 10);
            }
            myPost('../get/searchLinks', param, function (json) {
                if (json.code === 200) {
                    _this.list = json.data.list;
                    _this.total = json.data.rowCount;
                } else {
                    alert(json.error);
                }
            });
        },
        getLinkVersion: function () {
            var _this = this;
            myPost('../get/getLinkVersion', {'id': this.$parent.projectId}, function (json) {
                if (json.code === 200) {
                    _this.versions = [{id: 0, name: "请选择导入批次"}];
                    json.data.map(function (v) {
                        _this.versions.push({id: v.version, name: "第 " + v.version + " 批"});
                    });
                } else {
                    alert(json.error);
                }
            });
        },
        enableAll: function () {
            var _this = this;
            myPost('../save/updateProjectLinkByVersion', {'id': this.$parent.projectId, 'version': this.version}, function (json) {
                if (json.code === 200) {
                    _this.getList(0);
                } else {
                    alert(json.error);
                }
            });
        },
        enableOne: function (id, isDel) {
            var _this = this, val = parseInt(isDel, 10) === 1 ? 0 : 1;
            myPost('../save/updateProjectLinkById', {'id': id, 'val': val}, function (json) {
                if (json.code === 200) {
                    _this.getList(0);
                } else {
                    alert(json.error);
                }
            });
        }
    },
    components: {
        "paginate": paginate
    },
    ready: function() {
        this.getList(0);
        this.getLinkVersion();
    }
};

// ========================filter=============================================
Vue.filter('timeFilter', function(value) {
    return (value / 60).toFixed(2);
});

Vue.filter('stateFilter', function(value) {
    return ["未开始", "正在进行", "挂起", "完成", "结算", "归档"][value];
});

Vue.filter('percentFilter', function(value, total, total2) {
    value = parseInt(value, 10);
    total = parseInt(total, 10);
    total2 = total2 ? parseInt(total2, 10) : 0;
    total = total + total2;
    if (total > 0) {
        return getFixed(value, total, 100, 2) + " %";
    }

    return 0 + " %";
});

// ========================myPost===================================
function myPost(url, param, fun) {
    $.post(url, param, function(json) {
        if (json.code === 201) {
            window.location.reload();
        } else {
            fun(json);
        }
    }, "json");
}

// ========================chart===================================
function getFixed(a, b, c, len) {
    if (parseFloat(b) == 0) {
        return 0;
    }
    var str = (((parseFloat(a)) / parseFloat(b)) * c).toFixed(len) + "",
        index = str.indexOf(".");
    if (index > 0) {
        return str.substr(0, str.indexOf('.'));
    }
    return str;
}

function getPercent(v1, v2) {
    v1 = parseInt(v1, 10);
    v2 = parseInt(v2, 10);
    if (v2 === 0) {
        return 0;
    }

    return getFixed(v1, v2, 100, 2);
}

function chartCategory(id, data) {
    option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: { // 坐标轴指示器，坐标轴触发有效
                type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        grid: {
            top: '20px',
            left: '6px',
            right: '20px',
            bottom: '20px',
            containLabel: true
        },
        xAxis: [{
            type: 'value',
            max: '100'
        }],
        yAxis: [{
            type: 'category',
            axisTick: { show: true },
            data: ['响应率', '配额率', '转换率', '甄别率', '掉线率', 'IR', '完成率']
        }],
        series: [{
            name: 'percent',
            type: 'bar',
            label: {
                normal: {
                    show: true,
                    position: 'inside'
                }
            },
            data: [

                getPercent(parseInt(data.compete, 10) + parseInt(data.refuse, 10) + parseInt(data.full, 10), data.partIn),
                getPercent(data.full, data.partIn),
                getPercent(data.compete, data.partIn),
                getPercent(data.refuse, data.partIn),
                getPercent(data.unfinish, data.partIn),
                getPercent(data.compete, parseInt(data.compete, 10) + parseInt(data.refuse, 10)),
                getPercent(data.compete, parseInt(data.amount, 10))
            ]
        }]
    };
    var myChart = echarts.init(document.getElementById(id));
    myChart.setOption(option);
}

// ========================vue实例===================================
new Vue({
    el: "#ihq-context",
    data: {
        menus: [
            { text: "项目报表", component: "projectChart" },
            { text: "未归档项目", component: "projectList" },
            { text: "已归档项目", component: "projectListFinish" },
            { text: "项目类型", component: "projectType" },
            { text: "外包公司", component: "projectCompany" }
        ],
        mindex: 0,
        currentView: 'projectChart',
        preIndex: 0,
        preView: 'projectChart',
        projectId: "",
        base: "/index.php/"
    },
    methods: {
        changem: function(index, view) {
            this.mindex = index;
            this.preIndex = index;
            this.currentView = view;
            this.preView = view;
        },
        back: function() {
            this.mindex = this.preIndex;
            this.currentView = this.preView;
        },
        next: function(id, view) {
            this.mindex = -1;
            this.currentView = view;
            this.projectId = id;
        }
    },
    components: {
        "projectChart": projectChart,
        "projectList": projectList,
        "projectListFinish": projectListFinish,
        "projectAdd": projectAdd,
        "projectType": projectType,
        "projectCompany": projectCompany,
        "projectOutsource": projectOutsource,
        "projectProcess": projectProcess,
        "projectChartOne": projectChartOne,
        "projectLink": projectLink
    }
});
