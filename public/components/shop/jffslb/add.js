Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="计费名称" prop="fybz_name">
							<el-input  v-model="form.fybz_name" autoComplete="off" clearable  placeholder="请输入计费名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="计费公式" prop="fybz_jfgs">
							<el-select   style="width:100%" v-model="form.fybz_jfgs" filterable clearable placeholder="请选择计费公式">
								<el-option v-for="(item,i) in fybz_jfgss" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="标准单价" prop="fybz_bzdj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fybz_bzdj" clearable :min="0" placeholder="请输入标准单价"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="计费系数" prop="fybz_jfxs">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fybz_jfxs" clearable :min="0" placeholder="请输入计费系数"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="坏账率" prop="fybz_hzl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fybz_hzl" clearable :min="0" placeholder="请输入坏账率"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="状态" prop="fybz_status">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.fybz_status"></el-switch>
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
				fydy_id:'',
				fylx_id:'',
				xqgl_id:'',
				fybz_name:'',
				fybz_jfgs:'',
				fybz_hzl:0,
				fybz_status:1,
				fydy_id:'',
				fylx_id:'',
			},
			fybz_jfgss:[],
			loading:false,
			rules: {
				fybz_name:[
					{required: true, message: '计费名称不能为空', trigger: 'blur'},
				],
				fybz_jfgs:[
					{required: true, message: '计费公式不能为空', trigger: 'change'},
				],
				fybz_bzdj:[
					{required: true, message: '标准单价不能为空', trigger: 'blur'},
				],
				fybz_jfxs:[
					{required: true, message: '计费系数不能为空', trigger: 'blur'},
				],
				fybz_hzl:[
					{required: true, message: '坏账率不能为空', trigger: 'blur'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Jffslb/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fybz_jfgss = res.data.data.fybz_jfgss
					}
				})
			}
		}
	},
	methods: {
		open(){
			const myURL = new URL(window.location.href)
			const urlobj = param2Obj(myURL.href)
			this.form.fydy_id = urlobj.fydy_id
			this.form.fylx_id = urlobj.fylx_id
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Jffslb/add',this.form).then(res => {
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
