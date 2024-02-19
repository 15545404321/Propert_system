Vue.component('Update', {
	template: `
		<el-drawer title="修改"  direction="rtl" size="1220px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
			<el-tabs v-model="activeName">
				<el-tab-pane style="padding-top:10px"  label="基础信息" name="基础信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位编号" prop="cewei_name">
							<el-input  v-model="form.cewei_name" autoComplete="off" clearable  placeholder="请输入车位编号"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
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
				<el-row >
					<el-col :span="24">
						<el-form-item label="排序" prop="px">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.px" clearable :min="0" placeholder="请输入排序"/>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
				<el-tab-pane style="padding-top:10px"  label="扩展信息" name="扩展信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始日期" prop="cewei_start_time">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.cewei_start_time" clearable placeholder="请输入开始日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束日期" prop="cewei_end_time">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.cewei_end_time" clearable placeholder="请输入结束日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产编号" prop="cewei_zcbh">
							<el-input  v-model="form.cewei_zcbh" autoComplete="off" clearable  placeholder="请输入资产编号"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="备注信息" prop="cewei_remarks">
							<el-input  type="textarea" autoComplete="off" v-model="form.cewei_remarks"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入备注信息"/>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
			</el-tabs>
			</el-form>
			<div slot="footer" style="text-align:center;margin:0 0 30px 0">
				<el-button :size="size" style="width:35%;" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" style="width:35%;" @click="closeForm">取 消</el-button>
			</div>
			</div>
		</el-drawer>
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
				cewei_name:'',
				tccd_id:'',
				cwqy_id:'',
				cwzt_id:'',
				cewei_start_time:curentTime(),
				cewei_end_time:'',
				cewei_zcbh:'',
				member_id:'',
				cewei_remarks:'',
				cw_pltj:1,
				cw_num:0,
				cwlx_id:'',
			},
			tccd_ids:[],
			cwqy_ids:[],
			cwzt_ids:[],
			member_ids:[],
			cwlx_ids:[],
			loading:false,
			activeName:'基础信息',
			rules: {
				cewei_name:[
					{required: true, message: '车位编号不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '车位编号格式错误'}
				],
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
				cw_num:[
					{required: true, message: '新增数量不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '新增数量格式错误'}
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
			if(this.info.member_id && val){
				axios.post(base_url + '/Cewei/remoteMemberidList',{dataval:this.info.member_id}).then(res => {
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
			this.form.cewei_start_time = parseTime(this.form.cewei_start_time)
			this.form.cewei_end_time = parseTime(this.form.cewei_end_time)
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
					axios.post(base_url + '/Cewei/update',this.form).then(res => {
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
