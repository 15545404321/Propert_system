Vue.component('Fankui', {
	template: `
		<el-dialog title="工程反馈" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="工程师傅" prop="cname">
							<el-select   style="width:100%" v-model="form.cname" filterable clearable placeholder="请选择工程师傅">
								<el-option v-for="(item,i) in cnames" :key="i" :label="item.key" :value="item.val.toString()"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="处理反馈" prop="bxxx_fankui">
							<el-input  type="textarea" autoComplete="off" v-model="form.bxxx_fankui"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入处理反馈"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="处理时间" prop="bxxx_cltime">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.bxxx_cltime" clearable placeholder="请输入处理时间"></el-date-picker>
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
				cname:'',
				bxxx_fankui:'',
				bxxx_cltime:'',
			},
			member_ids:[],
			cnames:[],
			bxfl_ids:[],
			loading:false,
			rules: {
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
			if(this.info.member_id && val){
				axios.post(base_url + '/Bxxx/remoteMemberidList',{dataval:this.info.member_id}).then(res => {
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
			this.form.bxxx_cltime = parseTime(this.form.bxxx_cltime)
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
					axios.post(base_url + '/Bxxx/fankui',this.form).then(res => {
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
