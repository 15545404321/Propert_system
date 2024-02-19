Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="投诉客户" prop="member_id">
							<el-select  remote :remote-method="remoteMemberidList"  style="width:100%" v-model="form.member_id" filterable clearable placeholder="请选择投诉客户">
								<el-option v-for="(item,i) in member_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="投诉内容" prop="tsjy_tsnr">
							<el-input  type="textarea" autoComplete="off" v-model="form.tsjy_tsnr"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入投诉内容"/>
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
				shop_id:'',
				member_id:'',
				tsjy_tsnr:'',
			},
			member_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(this.info.member_id && val){
				axios.post(base_url + '/Tsjy/remoteMemberidList',{dataval:this.info.member_id}).then(res => {
					if(res.data.status == 200){
						this.member_ids = res.data.data
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
		},
		remoteMemberidList(val){
			axios.post(base_url + '/Tsjy/remoteMemberidList',{queryString:val}).then(res => {
				if(res.data.status == 200){
					this.member_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Tsjy/update',this.form).then(res => {
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
