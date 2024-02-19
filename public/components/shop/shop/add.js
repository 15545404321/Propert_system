Vue.component('Add', {
	template: `
		<el-drawer title="添加"  direction="rtl" size="1220px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
			<el-tabs v-model="activeName">
				<el-tab-pane style="padding-top:10px"  label="账号信息" name="账号信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="企业名称" prop="shop_name">
							<el-input  v-model="form.shop_name" autoComplete="off" clearable  placeholder="请输入企业名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="收款单位" prop="shop_skdw">
							<el-input  v-model="form.shop_skdw" autoComplete="off" clearable  placeholder="请输入收款单位"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户名称" prop="cname">
							<el-input  v-model="form.cname" autoComplete="off" clearable  placeholder="请输入用户名称"></el-input>
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
						<el-form-item label="用户密码" prop="password">
							<el-input  show-password autoComplete="off" v-model="form.password"  clearable placeholder="请输入用户密码"/>
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
				</el-tab-pane>
				<el-tab-pane style="padding-top:10px"  label="公司信息" name="公司信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="所在城市" prop="shop_address">
							<shengshiqu v-if="show" :checkstrictly="{ checkStrictly: false }" :type="1" :treeoption.sync="form.shop_address"></shengshiqu>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="详细地址" prop="shop_range">
							<el-input  v-model="form.shop_range" autoComplete="off" clearable  placeholder="请输入详细地址"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="　总经理" prop="shop_xlr">
							<el-input  v-model="form.shop_xlr" autoComplete="off" clearable  placeholder="请输入　总经理"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="联系电话" prop="shop_tel">
							<el-input  v-model="form.shop_tel" autoComplete="off" clearable  placeholder="请输入联系电话"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="企业邮箱" prop="shop_email">
							<el-input  v-model="form.shop_email" autoComplete="off" clearable  placeholder="请输入企业邮箱"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始日期" prop="start_date">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.start_date" clearable placeholder="请输入开始日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="到期日期" prop="end_date">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.end_date" clearable placeholder="请输入到期日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="项目上限" prop="restrict_num">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.restrict_num" clearable :min="0" placeholder="请输入项目上限"/>
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
				shop_name:'',
				shop_address:[],
				shop_range:'',
				shop_xlr:'',
				shop_tel:'',
				shop_email:'',
				start_date:curentTime(),
				end_date:'',
				shop_skdw:'',
				shop_id:'',
				root:'1',
				cname:'',
				account:'',
				password:'',
				create_time:'',
				update_time:'',
				disable:1,
			},
			loading:false,
			activeName:'账号信息',
			rules: {
				shop_name:[
					{required: true, message: '企业名称不能为空', trigger: 'blur'},
				],
				shop_address:[
					{required: true,type:'array', message: '所在城市不能为空', trigger: 'change'},
				],
				shop_range:[
					{required: true, message: '详细地址不能为空', trigger: 'blur'},
				],
				shop_xlr:[
					{required: true, message: '　总经理不能为空', trigger: 'blur'},
				],
				shop_tel:[
					{required: true, message: '联系电话不能为空', trigger: 'blur'},
					{pattern:/^1[3456789]\d{9}$/, message: '联系电话格式错误'}
				],
				shop_email:[
					{pattern:/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/, message: '企业邮箱格式错误'}
				],
				start_date:[
					{required: true, message: '开始日期不能为空', trigger: 'blur'},
				],
				end_date:[
					{required: true, message: '到期日期不能为空', trigger: 'blur'},
				],
				restrict_num:[
					{required: true, message: '项目上限不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '项目上限格式错误'}
				],
				shop_skdw:[
					{required: true, message: '收款单位不能为空', trigger: 'blur'},
				],
			}
		}
	},
	methods: {
		open(){
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Shop/add',this.form).then(res => {
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
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
