Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="岗位名称" prop="gwgl_gwmc">
							<el-input  v-model="form.gwgl_gwmc" autoComplete="off" clearable  placeholder="请输入岗位名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属项目" prop="xqgl_id">
							<el-select @change="selectGwgl_sjgw"  style="width:100%" v-model="form.xqgl_id" filterable clearable placeholder="请选择所属项目">
								<el-option v-for="(item,i) in xqgl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="上级岗位" prop="gwgl_sjgw">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.gwgl_sjgw" :options="gwgl_sjgws" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择上级岗位"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="岗位排序" prop="gwgl_px">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.gwgl_px" clearable :min="0" placeholder="请输入岗位排序"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row>
					<el-col :span="24">
						<el-form-item label="岗位权限" prop="gwgl_role">
							<el-tree class="tree-border" :data="options" :default-checked-keys="[base+'/Index/main']"  show-checkbox ref="menu" node-key="access" :check-strictly="false" empty-text="加载中，请稍后" :props="defaultProps"></el-tree>
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
	},
	data(){
		return {
			form: {
				shop_id:'',
				gwgl_gwmc:'',
				xqgl_id:'',
				gwgl_px:50,
				gwgl_role:[],
			},
			xqgl_ids:[],
			gwgl_sjgws:[],
			loading:false,
			defaultProps: {
				children: "children",
				label: "title"
			},
			options:[],
			strictly:false,
			base:base_url,
			rules: {
				gwgl_gwmc:[
					{required: true, message: '岗位名称不能为空', trigger: 'blur'},
				],
				xqgl_id:[
					{required: true, message: '所属项目不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Gwgl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.xqgl_ids = res.data.data.xqgl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.strictly = true
			axios.post(base_url+'/Gwgl/getRoleAccess').then(res => {
				if(res.data.status == 200){
					this.options = res.data.menus
				}
			})
		},
		selectGwgl_sjgw(val){
			this.$delete(this.form,'gwgl_sjgw')
			axios.post(base_url + '/Gwgl/getGwgl_sjgw',{xqgl_id:val}).then(res => {
				if(res.data.status == 200){
					this.gwgl_sjgws = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					this.form.gwgl_role = this.getMenuAllCheckedKeys()
					axios.post(base_url + '/Gwgl/add',this.form).then(res => {
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
		getMenuAllCheckedKeys() {
			let checkedKeys = this.$refs.menu.getCheckedKeys()
			let halfCheckedKeys = this.$refs.menu.getHalfCheckedKeys()
			checkedKeys.unshift.apply(checkedKeys, halfCheckedKeys)
			return checkedKeys
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
