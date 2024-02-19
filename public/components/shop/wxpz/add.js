Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="APPID" prop="app_id">
							<el-input  v-model="form.app_id" autoComplete="off" clearable  placeholder="请输入APPID"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="Secret" prop="secret">
							<el-input  v-model="form.secret" autoComplete="off" clearable  placeholder="请输入Secret"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="支付商户" prop="mch_id">
							<el-input  v-model="form.mch_id" autoComplete="off" clearable  placeholder="请输入支付商户"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="支付秘钥" prop="pay_sign_key">
							<el-input  v-model="form.pay_sign_key" autoComplete="off" clearable  placeholder="请输入支付秘钥"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="支付证书" prop="apiclient_cert">
							<Upload v-if="show" size="small" file_type="file"  file_ext="pem"   :file.sync="form.apiclient_cert"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="证书密钥" prop="apiclient_key">
							<Upload v-if="show" size="small" file_type="file"  file_ext="pem"   :file.sync="form.apiclient_key"></Upload>
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
	},
	data(){
		return {
			form: {
				app_id:'',
				secret:'',
				mch_id:'',
				pay_sign_key:'',
				apiclient_cert:'',
				apiclient_key:'',
				shop_id:'',
			},
			loading:false,
			rules: {
				app_id:[
					{required: true, message: 'APPID不能为空', trigger: 'blur'},
				],
				secret:[
					{required: true, message: 'Secret不能为空', trigger: 'blur'},
				],
				mch_id:[
					{required: true, message: '支付商户不能为空', trigger: 'blur'},
				],
				pay_sign_key:[
					{required: true, message: '支付秘钥不能为空', trigger: 'blur'},
				],
				apiclient_cert:[
					{required: true, message: '支付证书不能为空', trigger: 'blur'},
				],
				apiclient_key:[
					{required: true, message: '证书密钥不能为空', trigger: 'blur'},
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
					axios.post(base_url + '/Wxpz/add',this.form).then(res => {
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
