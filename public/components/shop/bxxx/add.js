Vue.component('Add', {
	template: `
		<el-dialog title="添加报修" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="报修描述" prop="bxxx_miaoshu">
							<el-input  type="textarea" autoComplete="off" v-model="form.bxxx_miaoshu"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入报修描述"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="图片信息" prop="bxxx_pic">
							<Upload v-if="show" size="small"    file_type="images" :images.sync="form.bxxx_pic"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属业主" prop="member_id">
							<el-select  remote :remote-method="remoteMemberidList"  style="width:100%" v-model="form.member_id" filterable clearable placeholder="请选择所属业主">
								<el-option v-for="(item,i) in member_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="问题分类" prop="bxfl_id">
							<el-select   style="width:100%" v-model="form.bxfl_id" filterable clearable placeholder="请选择问题分类">
								<el-option v-for="(item,i) in bxfl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
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
				shop_id:'',
				xqgl_id:'',
				bxxx_miaoshu:'',
				bxxx_pic:[],
				bxxx_time:'',
				member_id:'',
				bxfl_id:'',
			},
			member_ids:[],
			cnames:[],
			bxfl_ids:[],
			loading:false,
			rules: {
				bxxx_miaoshu:[
					{required: true, message: '报修描述不能为空', trigger: 'blur'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Bxxx/getFieldList').then(res => {
					if(res.data.status == 200){
						this.cnames = res.data.data.cnames
						this.bxfl_ids = res.data.data.bxfl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
		},
		remoteMemberidList(val){
			axios.post(base_url + '/Bxxx/remoteMemberidList',{queryString:val}).then(res => {
				if(res.data.status == 200){
					this.member_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Bxxx/add',this.form).then(res => {
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
			this.member_ids = []
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
