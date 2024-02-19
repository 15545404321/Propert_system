Vue.component('Update', {
	template: `
		<el-dialog title="修改状态" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="押金状态" prop="tui_status">
							<el-radio-group v-model="form.tui_status">
								<el-radio :label="1">已收取</el-radio>
								<el-radio :label="2">已退回</el-radio>
								<el-radio :label="3">转预存</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.tui_status != 1">
					<el-col :span="24">
						<el-form-item label="操作时间" prop="tui_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.tui_time" clearable placeholder="请输入操作时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.tui_status !=1">
					<el-col :span="24">
						<el-form-item label="押金备注" prop="tui_beizhu">
							<el-input  v-model="form.tui_beizhu" autoComplete="off" clearable  placeholder="请输入押金备注"></el-input>
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
				tui_status:1,
				tui_time:'',
				tui_beizhu:'',
			},
			fcxx_ids:[],
			fylx_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Yajin/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fylx_ids = res.data.data.fylx_ids
					}
				})
			}
			if(this.info.fcxx_id && val){
				axios.post(base_url + '/Yajin/remoteFcxxidList',{dataval:this.info.fcxx_id}).then(res => {
					if(res.data.status == 200){
						this.fcxx_ids = res.data.data
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.tui_time = parseTime(this.form.tui_time)
		},
		remoteFcxxidList(val){
			axios.post(base_url + '/Yajin/remoteFcxxidList',{queryString:val}).then(res => {
				if(res.data.status == 200){
					this.fcxx_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Yajin/update',this.form).then(res => {
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
			this.fcxx_ids = []
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
