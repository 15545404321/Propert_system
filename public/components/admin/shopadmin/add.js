Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="操作人员" prop="cname">
							<el-input  v-model="form.cname" autoComplete="off" clearable  placeholder="请输入操作人员"></el-input>
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
				shop_id:'',
				root:'1',
				cname:'',
				account:'',
				password:'',
				create_time:'',
				update_time:'',
				disable:1,
				shop_id:'',
				root:'',
			},
			loading:false,
			rules: {
				cname:[
					{required: true, message: '操作人员不能为空', trigger: 'blur'},
				],
				account:[
					{required: true, message: '用户账号不能为空', trigger: 'blur'},
				],
				password:[
					{required: true, message: '用户密码不能为空', trigger: 'blur'},
				],
			}
		}
	},
	methods: {
		open(){
			const myURL = new URL(window.location.href)
			const urlobj = param2Obj(myURL.href)
			this.form.shop_id = urlobj.shop_id
			this.form.root = urlobj.root
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/ShopAdmin/add',this.form).then(res => {
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
