Vue.component('Yezhufankui', {
	template: `
		<el-dialog title="业主反馈" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户评分" prop="bxxx_pingfen">
							<el-rate style="margin-top:6px;" v-model="form.bxxx_pingfen"></el-rate>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户评价" prop="bxxx_pingjia">
							<el-input  type="textarea" autoComplete="off" v-model="form.bxxx_pingjia"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入客户评价"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="处理状态" prop="bxxx_start">
							<el-radio-group v-model="form.bxxx_start">
								<el-radio :label="1">待处理</el-radio>
								<el-radio :label="2">已处理</el-radio>
								<el-radio :label="3">未处理</el-radio>
							</el-radio-group>
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
				bxxx_pingfen:'',
				bxxx_pingjia:'',
				bxxx_start:1,
			},
			member_ids:[],
			cnames:[],
			bxfl_ids:[],
			loading:false,
			rules: {
				bxxx_start:[
					{required: true, message: '处理状态不能为空', trigger: 'change'},
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
					axios.post(base_url + '/Bxxx/yezhufankui',this.form).then(res => {
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
