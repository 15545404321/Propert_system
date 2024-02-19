Vue.component('Sxupdate', {
	template: `
		<el-dialog title="更改属性" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="停车场地" prop="tccd_id">
							<el-select   style="width:100%" v-model="form.tccd_id" filterable clearable placeholder="请选择停车场地">
								<el-option v-for="(item,i) in tccd_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位区域" prop="cwqy_id">
							<el-select   style="width:100%" v-model="form.cwqy_id" filterable clearable placeholder="请选择车位区域">
								<el-option v-for="(item,i) in cwqy_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位状态" prop="cwzt_id">
							<el-select   style="width:100%" v-model="form.cwzt_id" filterable clearable placeholder="请选择车位状态">
								<el-option v-for="(item,i) in cwzt_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位面积" prop="cewei_cwmj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.cewei_cwmj" clearable :min="0" placeholder="请输入车位面积"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位类型" prop="cwlx_id">
							<el-select   style="width:100%" v-model="form.cwlx_id" filterable clearable placeholder="请选择车位类型">
								<el-option v-for="(item,i) in cwlx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
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
			},
			tccd_ids:[],
			cwqy_ids:[],
			cwzt_ids:[],
			member_ids:[],
			cwlx_ids:[],
			loading:false,
			rules: {
				tccd_id:[
					{required: true, message: '停车场地不能为空', trigger: 'change'},
				],
				cwqy_id:[
					{required: true, message: '车位区域不能为空', trigger: 'change'},
				],
				cwzt_id:[
					{required: true, message: '车位状态不能为空', trigger: 'change'},
				],
				cewei_cwmj:[
					{required: true, message: '车位面积不能为空', trigger: 'blur'},
				],
				cwlx_id:[
					{required: true, message: '车位类型不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Cewei/getFieldList').then(res => {
					if(res.data.status == 200){
						this.tccd_ids = res.data.data.tccd_ids
						this.cwqy_ids = res.data.data.cwqy_ids
						this.cwzt_ids = res.data.data.cwzt_ids
						this.cwlx_ids = res.data.data.cwlx_ids
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
			axios.post(base_url + '/Cewei/remoteMemberidList',{queryString:val}).then(res => {
				if(res.data.status == 200){
					this.member_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Cewei/sxupdate',this.form).then(res => {
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
