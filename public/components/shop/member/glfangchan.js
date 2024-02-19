Vue.component('Glfangchan', {
	template: `
		<el-dialog title="关联房产" width="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row>
					<el-col :span="24">
						<el-form-item label="房产信息" prop="fcxx_idx">
							<el-tree class="tree-border" @check="checkStrictly" :data="options" show-checkbox ref="menu" node-key="access"  :default-checked-keys="form.fcxx_idx" :check-strictly="strictly" empty-text="加载中，请稍后" :props="defaultProps" :check-strictly="true"></el-tree>
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
				fcxx_idx:[],
			},
			zjlx_ids:[],
			khlb_ids:[],
			khlx_ids:[],
			member_idxs:[],
			cewei_ids:[],
			car_ids:[],
			loading:false,
			defaultProps: {
				children: "children",
				label: "title"
			},
			options:[],
			strictly:false,
			base:base_url,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Member/getFieldList').then(res => {
					if(res.data.status == 200){
						this.zjlx_ids = res.data.data.zjlx_ids
						this.khlb_ids = res.data.data.khlb_ids
						this.khlx_ids = res.data.data.khlx_ids
						this.member_idxs = res.data.data.member_idxs
						this.cewei_ids = res.data.data.cewei_ids
						this.car_ids = res.data.data.car_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.setDefaultVal('fcxx_idx')
			this.strictly = true
			axios.post(base_url+'/Member/getRoleAccessFcxx').then(res => {
				if(res.data.status == 200){
					this.options = res.data.menus
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					this.form.fcxx_idx = this.getMenuAllCheckedKeys()
					axios.post(base_url + '/Member/glfangchan',this.form).then(res => {
						if(res.data.status == 200){
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							console.log('this.form.fcxx_idx',this.form.fcxx_idx)
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
		deselectcewei_id(node){
			const arr = this.form.cewei_id
			arr.splice(arr.findIndex(item => item == node.val),1)
		},
		getMenuAllCheckedKeys() {
			let checkedKeys = this.$refs.menu.getCheckedKeys()
			let halfCheckedKeys = this.$refs.menu.getHalfCheckedKeys()
			checkedKeys.unshift.apply(checkedKeys, halfCheckedKeys)
			return checkedKeys
		},
		checkStrictly(){
			this.strictly = true
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
