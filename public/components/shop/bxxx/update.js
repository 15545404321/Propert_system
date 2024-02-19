Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
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
						<el-form-item label="报修时间" prop="bxxx_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.bxxx_time" clearable placeholder="请输入报修时间"></el-date-picker>
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
		info: {
			type: Object,
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
				cname:'',
				bxxx_fankui:'',
				bxxx_cltime:'',
				bxxx_pingfen:'',
				bxxx_pingjia:'',
				bxxx_start:1,
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
			this.setDefaultVal('bxxx_pic')
			this.form.bxxx_time = parseTime(this.form.bxxx_time)
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
					axios.post(base_url + '/Bxxx/update',this.form).then(res => {
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
		setDefaultVal(key){
			if(this.form[key] == null || this.form[key] == ''){
				this.form[key] = []
			}
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
