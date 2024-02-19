Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="上级菜单" prop="pid">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.pid" :options="pids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择上级菜单"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="导航名称" prop="title">
							<el-input  v-model="form.title" autoComplete="off" clearable  placeholder="请输入导航名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="排序字段" prop="sort">
							<el-input  v-model="form.sort" autoComplete="off" clearable  placeholder="请输入排序字段"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="菜单类型" prop="type">
							<el-radio-group v-model="form.type">
								<el-radio :label="0">顶级菜单</el-radio>
								<el-radio :label="1">跳转链接</el-radio>
								<el-radio :label="2">点击内容</el-radio>
								<el-radio :label="3">跳转小程序</el-radio>
								<el-radio :label="4">展示图片</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="跳转url" prop="url">
							<el-input  v-model="form.url" autoComplete="off" clearable  placeholder="请输入跳转url"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="小程序appid" prop="xcx_appid">
							<el-input  v-model="form.xcx_appid" autoComplete="off" clearable  placeholder="请输入小程序appid"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="小程序url" prop="xcx_url">
							<el-input  v-model="form.xcx_url" autoComplete="off" clearable  placeholder="请输入小程序url"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="点击code" prop="cont_code">
							<el-input  v-model="form.cont_code" autoComplete="off" clearable  placeholder="请输入点击code"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="素材id" prop="media_id">
							<el-input  v-model="form.media_id" autoComplete="off" clearable  placeholder="请输入素材id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
		'treeselect':VueTreeselect.Treeselect,
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				shop_id:'',
				pid:0,
				title:'',
				sort:'',
				type:0,
				url:'',
				xcx_appid:'',
				xcx_url:'',
				cont_code:'',
				media_id:'',
			},
			pids:[],
			loading:false,
			rules: {
				title:[
					{required: true, message: '导航名称不能为空', trigger: 'blur'},
				],
				type:[
					{required: true, message: '菜单类型不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/ShopWxConfig/getFieldList').then(res => {
					if(res.data.status == 200){
						this.pids = res.data.data.pids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/ShopWxConfig/update',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
