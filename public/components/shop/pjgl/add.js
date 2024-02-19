Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="票据类型" prop="pjlx_id">
							<el-select @change="selectPjlx_pid"  style="width:100%" v-model="form.pjlx_id" filterable clearable placeholder="请选择票据类型">
								<el-option v-for="(item,i) in pjlx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="票据模板" prop="pjlx_pid">
							<el-select   style="width:100%" v-model="form.pjlx_pid" filterable clearable placeholder="请选择票据模板">
								<el-option v-for="(item,i) in pjlx_pids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="票据名称" prop="pjgl_name">
							<el-input  v-model="form.pjgl_name" autoComplete="off" clearable  placeholder="请输入票据名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="起始编码" prop="pjgl_qsbm">
							<el-input  v-model="form.pjgl_qsbm" autoComplete="off" clearable  placeholder="请输入起始编码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="票据张数" prop="pjgl_pjzs">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.pjgl_pjzs" clearable :min="0" placeholder="请输入票据张数"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="启用状态" prop="pjgl_status">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.pjgl_status"></el-switch>
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
				pjlx_id:'',
				pjlx_pid:'',
				pjgl_name:'',
				pjgl_qsbm:'',
				pjgl_pjzs:1000000,
				shop_admin_id:'',
				pjgl_time:'',
				pjgl_status:1,
				shop_id:'',
				xqgl_id:'',
			},
			pjlx_ids:[],
			pjlx_pids:[],
			loading:false,
			rules: {
				pjlx_id:[
					{required: true, message: '票据类型不能为空', trigger: 'change'},
				],
				pjlx_pid:[
					{required: true, message: '票据模板不能为空', trigger: 'change'},
				],
				pjgl_name:[
					{required: true, message: '票据名称不能为空', trigger: 'blur'},
				],
				pjgl_qsbm:[
					{required: true, message: '起始编码不能为空', trigger: 'blur'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Pjgl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.pjlx_ids = res.data.data.pjlx_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
		},
		selectPjlx_pid(val){
			this.form.pjlx_pid = ''
			axios.post(base_url + '/Pjgl/getPjlx_pid',{pjlx_id:val}).then(res => {
				if(res.data.status == 200){
					this.pjlx_pids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Pjgl/add',this.form).then(res => {
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
