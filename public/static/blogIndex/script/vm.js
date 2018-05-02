/**
 * 分页组件
 * page-Res          项目总数量 从父组件中通过props获取
 * slice     =>     每页显示的项目数量
 * index     =>     当前页码
 *
 * pageTotal =>     总页数
 * slices    =>     截取的结束位置     根据event传给父组件
 * initial   =>     截取的起始位      根据event传给父组件
 * 父组件通过获取的slices 和 initial 将项目 截取合适的值在页面中显示
 */
Vue.component('page', {
    template: `<div><nav aria-label="Page navigation">
            <ul class="pagination">
                <li>
                    <a href="#" aria-label="Previous" @click="pageIndex(-1)">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li  v-for="(item, index) in pageTotal" :class="{active: index == pageData.index}"><a href="javascript:;" @click="page(index)">{{index+1}}</a></li>
                <li>
                    <a href="#" aria-label="Next" @click="pageIndex(1)">
                            <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav></div>`,
    data() {
        return {
            pageData: {
                slice: 4,
                index: '',
            }
        }
    },
    props: ['page-Res'],
    mounted: function() {
        this.$nextTick(function() {
            this.getChild();
        })
    },
    methods: {
        /**
         * 触发组件中的自定义事件, 事件将调用父组件的函数获取到子组件的数据
         * @return {[type]} [description]
         */
        getChild: function() {
            this.$emit('get', { 'slices': this.slices, 'initial': this.initial });
        },
        /**
         * 根据当前页码的变化, 将修改后的值传递给父组件,且重新加载页面数据
         * @param  {[type]} index 当前页码
         */
        page: function(index) {
            this.pageData.index = index
            this.$emit('get', { 'slices': this.slices, 'initial': this.initial });
            this.$emit('load');
        },
        /**
         * 获取当前的页码
         * @param  {[type]} flag [description]
         * @return {[type]}      [description]
         */
        pageIndex: function(flag) {
            if (flag > 0) {
                if (this.pageData.index == this.pageTotal.length - 1) {
                    this.pageData.index = this.pageTotal.length - 1
                } else {
                    this.pageData.index++
                        this.$emit('get', { 'slices': this.slices, 'initial': this.initial });
                    this.$emit('load');
                }
            } else {
                if (this.pageData.index == 0) {
                    this.pageData.index = 0
                } else {
                    this.pageData.index--
                        this.$emit('get', { 'slices': this.slices, 'initial': this.initial });
                        this.$emit('load');
                }
            }
        },
    },
    computed: {
        pageTotal: function() {
            var arr = new Array()
            var c = (this.pageRes.length) / this.pageData.slice

            for (var i = 0; i < c; i++) {
                arr.push(i)
            }
            return arr
        },
        /**
         * 获取截取的终点位置
         * @return {[type]} [description]
         */
        slices: function() {
            return this.pageData.slice + this.pageData.index * this.pageData.slice
        },
        /**
         * 获取截取的起始位置
         * @return {[type]} [description]
         */
        initial: function() {
            return 0 + this.pageData.index * this.pageData.slice
        }
    }
})



var vm = new Vue({
    el: "#app",
    data: {
        delId: [],
        res: [],
        pageRes: [], //获取的数据原始长度,作用获取分页页数
    },
    mounted: function() {
        this.$nextTick(function() {
            this.load()
        })
    },
    methods: {
        /**
         * 经过子组件的触发,调用该函数,将子组件的数据传递给父组件
         * @param  {[type]} data 子组件中 $emit 函数的第二个参数,将子组件的数据赋予父组件中的属性
         */
        getData: function(data) {
            this.initial = data.initial
            this.slices = data.slices
        },
        axios: function(url, data, fn) {
            // console.log(data)
            var _this = this
            axios({
                    url: url,
                    method: 'post',
                    data: data,
                    transformRequest: [function(data) {
                        // Do whatever you want to transform the data
                        let ret = ''
                        for (let it in data) {
                            ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
                        }
                        return ret
                    }],
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    }
                }).then((response) => {
                    if (fn) fn()
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        load: function() {
            var _this = this;
            axios.get('index/Index/getData').then(function(response) {
                console.log(response)
                _this.pageRes = response.data
                _this.pageRes.forEach(function(item) {
                    _this.$set(item, 'checked', false)
                })
                // if (_this.pageRes[0].arc_id) {
                //     _this.pageRes = _this.pageRes.filter(item => item.is_recycle)
                // }
                //根据子组件赋予的属性, 获取需要显示的数据数量
                _this.res = _this.pageRes.slice(_this.initial, _this.slices)

            }).catch(function(error) {
                console.log(error)
            });
        },
        recycleFn: function() {
            var data = new Array()
            data = this.delIds
            this.axios('recycle', data)
            this.load();
        },
    },
    computed: {
        checkFlag: {
            get: function() {
                return this.res.every(item => item.checked)
            },
            set: function(val) {
                this.res.forEach(item => item.checked = val)
            }
        },
        delIds: {
            get: function() {
                var obj = this.res.filter(item => item.checked)
                var ids = new Array()

                obj.forEach(function(item) {
                    if (item.admin_id) {
                        ids.length = 0
                        obj.forEach(item => ids.push(item.admin_id))
                    } else if (item.arc_id) {
                        ids.length = 0
                        obj.forEach(item => ids.push(item.arc_id))
                    } else if (item.cate_id) {
                        ids.length = 0
                        obj.forEach(item => ids.push(item.cate_id))
                    } else if (item.tag_id) {
                        ids.length = 0
                        obj.forEach(item => ids.push(item.tag_id))
                    }
                })
                return ids
            },
        },
    },
})