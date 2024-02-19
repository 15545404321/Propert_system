Vue.component('Update', {
	template: `
		<el-dialog title="重新生成" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始时间" prop="lsys_kstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.lsys_kstime" clearable placeholder="请输入开始时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束时间" prop="lsys_jstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.lsys_jstime" clearable placeholder="请输入结束时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="建筑类型" prop="jflx_id">
							<el-select @change="selectFcxx_idx"  style="width:100%" v-model="form.jflx_id" filterable clearable placeholder="请选择建筑类型">
								<el-option v-for="(item,i) in jflx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用类型" prop="fydy_id">
							<el-select @change="selectFybz_id"  style="width:100%" v-model="form.fydy_id" filterable clearable placeholder="请选择费用类型">
								<el-option v-for="(item,i) in fydy_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用标准" prop="fybz_id">
							<el-select   style="width:100%" v-model="form.fybz_id" filterable clearable placeholder="请选择费用标准">
								<el-option v-for="(item,i) in fybz_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row>
					<el-col :span="24">
						<el-form-item label="收费资产" prop="fcxx_idx">
							<el-tree class="tree-border" @check="checkStrictly" :data="options" show-checkbox ref="menu" node-key="access"  :default-checked-keys="form.fcxx_idx" :check-strictly="strictly" empty-text="加载中，请稍后" :props="defaultProps"></el-tree>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="应收金额" prop="lsys_ysje">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.lsys_ysje" clearable :min="0" placeholder="请输入应收金额"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="备注" prop="lsys_bz">
							<el-input  v-model="form.lsys_bz" autoComplete="off" clearable  placeholder="请输入备注"></el-input>
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
				lsys_kstime:'',
				lsys_jstime:'',
				jflx_id:'',
				fydy_id:'',
				fybz_id:'',
				fcxx_idx:[],
				lsys_bz:'',
				shop_id:'',
				xqgl_id:'',
			},
			jflx_ids:[],
			fydy_ids:[],
			fybz_ids:[],
			loading:false,
			defaultProps: {
				children: "children",
				label: "title"
			},
			options:[],
			strictly:false,
			base:base_url,
			rules: {
				lsys_kstime:[
					{required: true, message: '开始时间不能为空', trigger: 'blur'},
				],
				lsys_jstime:[
					{required: true, message: '结束时间不能为空', trigger: 'blur'},
				],
				jflx_id:[
					{required: true, message: '建筑类型不能为空', trigger: 'change'},
				],
				fydy_id:[
					{required: true, message: '费用类型不能为空', trigger: 'change'},
				],
				/*fcxx_idx:[
					{required: true, message: '收费资产不能为空', trigger: ''},
				],*/
				lsys_ysje:[
					{required: true, message: '应收金额不能为空', trigger: 'blur'},
					{pattern:/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/, message: '应收金额格式错误'}
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Lsys/getFcxx_idx',{jflx_id:this.info.jflx_id}).then(res => {
					if(res.data.status == 200){
						this.fcxx_idxs = res.data.data
					}
				})
			}
			if(val){
				axios.post(base_url + '/Lsys/getFybz_id',{fydy_id:this.info.fydy_id}).then(res => {
					if(res.data.status == 200){
						this.fybz_ids = res.data.data
					}
				})
			}
			if(val){
				axios.post(base_url + '/Lsys/getFieldList').then(res => {
					if(res.data.status == 200){
						this.jflx_ids = res.data.data.jflx_ids
						this.fydy_ids = res.data.data.fydy_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.lsys_kstime = parseTime(this.form.lsys_kstime)
			this.form.lsys_jstime = parseTime(this.form.lsys_jstime)
			this.setDefaultVal('fcxx_idx')
			this.strictly = true
			axios.post(base_url+'/Lsys/getRoleAccessUpdate').then(res => {
				if(res.data.status == 200){
					this.options = res.data.menus
				}
			})
		},
		selectFcxx_idx(val){
			this.form.fcxx_idx = ''
			axios.post(base_url + '/Lsys/getFcxx_idx',{jflx_id:val}).then(res => {
				if(res.data.status == 200){
					this.fcxx_idxs = res.data.data
				}
			})
		},
		selectFybz_id(val){
			this.form.fybz_id = ''
			axios.post(base_url + '/Lsys/getFybz_id',{fydy_id:val}).then(res => {
				if(res.data.status == 200){
					this.fybz_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					this.form.fcxx_idx = this.getMenuAllCheckedKeys()
					axios.post(base_url + '/Lsys/update',this.form).then(res => {
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
		getMenuAllCheckedKeys() {
			let checkedKeys = this.$refs.menu.getCheckedKeys()
			let halfCheckedKeys = this.$refs.menu.getHalfCheckedKeys()
			checkedKeys.unshift.apply(checkedKeys, halfCheckedKeys)
			return checkedKeys
		},
		checkStrictly(){
			this.strictly = false
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
