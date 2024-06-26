Vue.component('Resetpwd', {
	template: `
		<el-dialog title="重置密码" width="500px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" label-width="80px">
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户密码" prop="password">
							<el-input  show-password autoComplete="off" v-model="form.password"  clearable placeholder="请输入用户密码"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="确认密码" prop="repassword">
							<el-input  show-password autoComplete="off" v-model="form.repassword"  clearable placeholder="请输入确认密码"/>
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
		var validatePass2 = (rule, value, callback) => {
			if (value === '') {
				callback(new Error('请再次输入密码'))
			} else if (value !== this.form.password) {
				callback(new Error('两次输入密码不一致!'))
			} else {
				callback()
			}
		}
		return {
			form: {
			},
			loading:false,
			rules: {
				password:[
					{required: true, message: '用户密码不能为空', trigger: 'blur'},
				],
				repassword:[
					{required: true, validator: validatePass2, trigger: 'blur'},
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
					axios.post(base_url + '/ShopAdmin/resetPwd',this.form).then(res => {
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
