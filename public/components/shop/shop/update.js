Vue.component('Update', {
	template: `
		<el-drawer title="修改"  direction="rtl" size="1220px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="企业名称" prop="shop_name">
							<el-input  v-model="form.shop_name" autoComplete="off" clearable  placeholder="请输入企业名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
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
						<el-form-item label="收款单位" prop="shop_skdw">
							<el-input  v-model="form.shop_skdw" autoComplete="off" clearable  placeholder="请输入收款单位"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
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
		info: {
			type: Object,
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
				shop_skdw:'',
			},
			loading:false,
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
				shop_skdw:[
					{required: true, message: '收款单位不能为空', trigger: 'blur'},
				],
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Shop/update',this.form).then(res => {
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
