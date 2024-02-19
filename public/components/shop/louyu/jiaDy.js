Vue.component('Jiady', {
	template: `
		<el-dialog title="追加单元" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" label-width="80px">
				<el-row >
					<el-col :span="24">
						<el-form-item label="追加数量" prop="louyu_dysl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.louyu_dysl" clearable :min="0" placeholder="请输入单元数量"/>
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
			},
			louyu_pids:[],
			louyutype_ids:[],
			louyusx_ids:[],
			loading:false,
			rules: {
				louyu_dysl:[
					{required: true, message: '单元数量不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '单元数量格式错误'}
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Louyu/getFieldList').then(res => {
					if(res.data.status == 200){
						this.louyu_pids = res.data.data.louyu_pids
						this.louyutype_ids = res.data.data.louyutype_ids
						this.louyusx_ids = res.data.data.louyusx_ids
					}
				})
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
					axios.post(base_url + '/Louyu/jiaDy',this.form).then(res => {
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
