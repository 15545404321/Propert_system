Vue.component('Add', {
	template: `
		<el-drawer title="添加"  direction="rtl" size="1220px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
			<el-tabs v-model="activeName">
				<el-tab-pane style="padding-top:10px"  label="基础信息" name="基础信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户名称" prop="member_name">
							<el-input  v-model="form.member_name" autoComplete="off" clearable  placeholder="请输入客户名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户手机" prop="member_tel">
							<el-input  v-model="form.member_tel" autoComplete="off" clearable  placeholder="请输入客户手机"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户生日" prop="member_birthday">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.member_birthday" clearable placeholder="请输入客户生日"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户性别" prop="member_sex">
							<el-radio-group v-model="form.member_sex">
								<el-radio :label="1">男</el-radio>
								<el-radio :label="2">女</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户类别" prop="khlb_id">
							<el-select   style="width:100%" v-model="form.khlb_id" filterable clearable placeholder="请选择客户类别">
								<el-option v-for="(item,i) in khlb_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户类型" prop="khlx_id">
							<el-select   style="width:100%" v-model="form.khlx_id" filterable clearable placeholder="请选择客户类型">
								<el-option v-for="(item,i) in khlx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
				<el-tab-pane style="padding-top:10px"  label="证件信息" name="证件信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="证件类型" prop="zjlx_id">
							<el-select   style="width:100%" v-model="form.zjlx_id" filterable clearable placeholder="请选择证件类型">
								<el-option v-for="(item,i) in zjlx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="证件号码" prop="member_zjhm">
							<el-input  v-model="form.member_zjhm" autoComplete="off" clearable  placeholder="请输入证件号码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="业主照片" prop="member_yzzp">
							<Upload v-if="show" size="small"  upload_type="1"    file_type="image" :image.sync="form.member_yzzp"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="证件照片" prop="member_zjzp">
							<Upload v-if="show" size="small"  upload_type="1"    file_type="image" :image.sync="form.member_zjzp"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
				<el-tab-pane style="padding-top:10px"  label="补充信息" name="补充信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="家庭成员" prop="member_idx">
							<el-select multiple style="width:100%" v-model="form.member_idx" filterable clearable placeholder="请选择家庭成员">
								<el-option v-for="(item,i) in member_idxs" :key="i" :label="item.key" :value="item.val.toString()"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="住卡数量" prop="member_hksl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.member_hksl" clearable :min="0" placeholder="请输入住卡数量"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户职业" prop="member_khzy">
							<el-input  v-model="form.member_khzy" autoComplete="off" clearable  placeholder="请输入客户职业"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="户口地址" prop="member_hkdz">
							<el-input  v-model="form.member_hkdz" autoComplete="off" clearable  placeholder="请输入户口地址"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="工作单位" prop="member_gzdw">
							<el-input  v-model="form.member_gzdw" autoComplete="off" clearable  placeholder="请输入工作单位"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="公司简介" prop="member_gsjj">
							<el-input  type="textarea" autoComplete="off" v-model="form.member_gsjj"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入公司简介"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="备注信息" prop="member_remark">
							<el-input  type="textarea" autoComplete="off" v-model="form.member_remark"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入备注信息"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房源确认" prop="member_fyqrrq">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.member_fyqrrq" clearable placeholder="请输入房源确认"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="入户通知" prop="member_rhtzrq">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.member_rhtzrq" clearable placeholder="请输入入户通知"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
			</el-tabs>
			</el-form>
			<div slot="footer" style="text-align:center;margin:0 0 30px 0">
				<el-button :size="size" style="width:35%;" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" style="width:35%;" @click="closeForm">取 消</el-button>
			</div>
			</div>
		</el-drawer>
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
				xqgl_id:'',
				member_name:'',
				member_tel:'',
				member_birthday:'',
				member_sex:1,
				zjlx_id:'',
				member_zjhm:'',
				khlb_id:'',
				khlx_id:'',
				member_idx:[],
				fcxx_idx:[],
				cewei_id:[],
				car_id:[],
				member_khzy:'',
				member_hkdz:'',
				member_gzdw:'',
				member_gsjj:'',
				member_remark:'',
				member_fyqrrq:'',
				member_rhtzrq:'',
				member_yzzp:'',
				member_zjzp:'',
			},
			zjlx_ids:[],
			khlb_ids:[],
			khlx_ids:[],
			member_idxs:[],
			cewei_ids:[],
			car_ids:[],
			loading:false,
			activeName:'基础信息',
			defaultProps: {
				children: "children",
				label: "title"
			},
			options:[],
			strictly:false,
			base:base_url,
			rules: {
				member_name:[
					{required: true, message: '客户名称不能为空', trigger: 'blur'},
				],
				member_tel:[
					{pattern:/^1[3456789]\d{9}$/, message: '客户手机格式错误'}
				],
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
			this.strictly = true
			axios.post(base_url+'/Member/getRoleAccess').then(res => {
				if(res.data.status == 200){
					this.options = res.data.menus
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					
					axios.post(base_url + '/Member/add',this.form).then(res => {
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
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
