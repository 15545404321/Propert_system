Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="人员姓名" prop="cname">
							<el-input  v-model="form.cname" autoComplete="off" clearable  placeholder="请输入人员姓名"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开资项目" prop="xqgl_id">
							<el-select @change="selectZzglAndGwgl"  style="width:100%" v-model="form.xqgl_id" filterable clearable placeholder="请选择开资项目">
								<el-option v-for="(item,i) in xqgl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属部门" prop="zzgl_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.zzgl_id" :options="zzgl_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择所属部门"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属岗位" prop="gwgl_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.gwgl_id" :options="gwgl_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择所属岗位"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户账号" prop="account">
							<el-input  v-model="form.account" autoComplete="off" clearable  placeholder="请输入用户账号"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户手机" prop="tel">
							<el-input  v-model="form.tel" autoComplete="off" clearable  placeholder="请输入用户手机"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="是否启用" prop="disable">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.disable"></el-switch>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="管理小区" prop="xqgl_ids">
							<el-select multiple style="width:100%" v-model="form.xqgl_ids" filterable clearable placeholder="请选择管理小区">
								<el-option v-for="(item,i) in xqgl_idss" :key="i" :label="item.key" :value="item.val.toString()"></el-option>
							</el-select>
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
				cname:'',
				xqgl_id:'',
				account:'',
				tel:'',
				update_time:'',
				disable:1,
				xqgl_ids:[],
			},
			xqgl_ids:[],
			zzgl_ids:[],
			gwgl_ids:[],
			xqgl_idss:[],
			loading:false,
			rules: {
				cname:[
					{required: true, message: '人员姓名不能为空', trigger: 'blur'},
				],
				xqgl_id:[
					{required: true, message: '开资项目不能为空', trigger: 'change'},
				],
				zzgl_id:[
					{required: true, message: '所属部门不能为空', trigger: 'change'},
				],
				gwgl_id:[
					{required: true, message: '所属岗位不能为空', trigger: 'change'},
				],
				account:[
					{required: true, message: '用户账号不能为空', trigger: 'blur'},
				],
				tel:[
					{required: true, message: '用户手机不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '用户手机格式错误'}
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/ShopAdmin/getFieldList').then(res => {
					if(res.data.status == 200){
						this.xqgl_ids = res.data.data.xqgl_ids
						this.xqgl_idss = res.data.data.xqgl_idss
					}
				})
			}
			if(val){
				axios.post(base_url + '/ShopAdmin/getGwgl_id',{xqgl_id:this.info.xqgl_id}).then(res => {
					if(res.data.status == 200){
						this.gwgl_ids = res.data.data
					}
				})
			}
			if(val){
				axios.post(base_url + '/ShopAdmin/getZzgl_id',{xqgl_id:this.info.xqgl_id}).then(res => {
					if(res.data.status == 200){
						this.zzgl_ids = res.data.data
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
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
			this.setDefaultVal('xqgl_ids')
		},
		selectZzglAndGwgl(val) {
			this.selectZzgl_id(val)
			this.selectGwgl_id(val)
		},
		selectZzgl_id(val){
			this.$delete(this.form,'zzgl_id')
			axios.post(base_url + '/ShopAdmin/getZzgl_id',{xqgl_id:val}).then(res => {
				if(res.data.status == 200){
					this.zzgl_ids = res.data.data
				}
			})
		},
		selectGwgl_id(val){
			this.$delete(this.form,'gwgl_id')
			axios.post(base_url + '/ShopAdmin/getGwgl_id',{xqgl_id:val}).then(res => {
				if(res.data.status == 200){
					this.gwgl_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/ShopAdmin/update',this.form).then(res => {
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
		setDefaultVal(key){
			if(this.form[key] == null || this.form[key] == ''){
				this.form[key] = []
			}
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
