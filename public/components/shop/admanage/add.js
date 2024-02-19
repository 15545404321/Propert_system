Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="广告图片" prop="admanage_pic">
							<Upload v-if="show" size="small"      file_type="image"  :image.sync="form.admanage_pic"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属页面" prop="admanage_page">
							<el-select style="width:100%" v-model="form.admanage_page" filterable clearable placeholder="请选择所属页面">
								<el-option key="0" label="服务月报" :value="1"></el-option>
								<el-option key="1" label="申请报修" :value="2"></el-option>
								<el-option key="2" label="便民电话" :value="3"></el-option>
								<el-option key="3" label="我的中心" :value="4"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.admanage_page == 4">
					<el-col :span="24">
						<el-form-item label="页面位置" prop="admanage_position">
							<el-select style="width:100%" v-model="form.admanage_position" filterable clearable placeholder="请选择页面位置">
								<el-option key="0" label="顶图" :value="1"></el-option>
								<el-option key="1" label="中图" :value="2"></el-option>
							</el-select>
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
				admanage_pic:'',
				shop_id:'',
			},
			loading:false,
			rules: {
				admanage_pic:[
					{required: true, message: '广告图片不能为空', trigger: 'blur'},
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
					axios.post(base_url + '/AdManage/add',this.form).then(res => {
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
