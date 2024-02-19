Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="标题设置" prop="pjmb_title">
							<el-input  v-model="form.pjmb_title" autoComplete="off" clearable  placeholder="请输入标题设置"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="纸张宽度" prop="pjmb_kuan">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.pjmb_kuan" clearable :min="0" placeholder="请输入纸张宽度"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="纸张高度" prop="pjmb_gao">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.pjmb_gao" clearable :min="0" placeholder="请输入纸张高度"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="公章打印" prop="pjgl_gzdy">
							<el-radio-group v-model="form.pjgl_gzdy">
								<el-radio :label="1">不打印</el-radio>
								<el-radio :label="2">打印</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.pjgl_gzdy  == 2">
					<el-col :span="24">
						<el-form-item label="公章上传" prop="pjgl_gongzhang">
							<Upload v-if="show" size="small"      file_type="image" :image.sync="form.pjgl_gongzhang"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.pjgl_gzdy  == 2">
					<el-col :span="24">
						<el-form-item label="水平位置" prop="pimb_gzwz">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.pimb_gzwz" clearable :min="0" placeholder="请输入水平位置"/>
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
				pjgl_id:'',
				pjmb_title:'',
				pjmb_kuan:241.3,
				pjmb_gao:93.98,
				pjgl_gzdy:1,
				pjgl_gongzhang:'',
				shop_id:'',
				xqgl_id:'',
			},
			loading:false,
			rules: {
				pjgl_id:[
					{required: true, message: '票据名称不能为空', trigger: ''},
				],
				pjmb_kuan:[
					{required: true, message: '纸张宽度不能为空', trigger: 'blur'},
					{pattern:/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/, message: '纸张宽度格式错误'}
				],
				pjmb_gao:[
					{required: true, message: '纸张高度不能为空', trigger: 'blur'},
				],
				pjgl_gzdy:[
					{required: true, message: '公章打印不能为空', trigger: 'change'},
				],
				pimb_gzwz:[
					{pattern:/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/, message: '水平位置格式错误'}
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
					axios.post(base_url + '/Pjmb/update',this.form).then(res => {
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
