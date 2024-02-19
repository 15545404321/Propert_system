Vue.component('Update', {
	template: `
		<div v-if="show">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="选择类型" prop="fylx_id">
							<el-radio-group v-model="form.fylx_id">
								<el-radio  v-for="(item,i) in fylx_ids" :key="i" :label="item.val">{{item.key}}</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用名称" prop="fydy_name">
							<el-input  v-model="form.fydy_name" autoComplete="off" clearable  placeholder="请输入费用名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="选择类别" prop="fylb_id">
							<el-select   style="width:100%" v-model="form.fylb_id" filterable clearable placeholder="请选择选择类别">
								<el-option v-for="(item,i) in fylb_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用单位" prop="fydw_id">
							<el-select   style="width:100%" v-model="form.fydw_id" filterable clearable placeholder="请选择费用单位">
								<el-option v-for="(item,i) in fydw_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="计费类型" prop="jflx_id">
							<el-select   style="width:100%" v-model="form.jflx_id" filterable clearable placeholder="请选择计费类型">
								<el-option v-for="(item,i) in jflx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="取整方式" prop="qzfs_id">
							<el-select   style="width:100%" v-model="form.qzfs_id" filterable clearable placeholder="请选择取整方式">
								<el-option v-for="(item,i) in qzfs_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开具发票" prop="fydy_kjfp">
							<el-radio-group v-model="form.fydy_kjfp">
								<el-radio :label="1">是</el-radio>
								<el-radio :label="0">否</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="应收月份" prop="fydy_ysyf">
							<el-select style="width:100%" v-model="form.fydy_ysyf" filterable clearable placeholder="请选择应收月份">
								<el-option key="0" label="计费开始日期所在月" :value="1"></el-option>
								<el-option key="1" label="计费结束日期所在月" :value="0"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="应收日份" prop="fydy_ysr">
							<el-radio-group v-model="form.fydy_ysr">
								<el-radio :label="1">指定月末</el-radio>
								<el-radio :label="2">指定日期</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.fylx_id == 3">
					<el-col :span="24">
						<el-form-item label="冲预收款" prop="fydy_cyskxm">
							<el-select multiple style="width:100%" v-model="form.fydy_cyskxm" filterable clearable placeholder="请选择冲预收款">
								<el-option v-for="(item,i) in fydy_cyskxms" :key="i" :label="item.key" :value="item.val.toString()"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.fydy_ysr == 2">
					<el-col :span="24">
						<el-form-item label="指定日期" prop="fydy_zdr">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.fydy_zdr" clearable placeholder="请输入指定日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="延迟月数" prop="fydy_ycys">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fydy_ycys" clearable :min="0" placeholder="请输入延迟月数"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="违约金比" prop="fydy_wyjbl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fydy_wyjbl" clearable :min="0" placeholder="请输入违约金比"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="备注信息" prop="fydy_remarks">
							<el-input  type="textarea" autoComplete="off" v-model="form.fydy_remarks"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入备注信息"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-form-item>
					<el-button :size="size" type="primary" @click="submit">保存设置</el-button>
					<el-button :size="size" icon="el-icon-back" @click="closeForm">返回</el-button>
				</el-form-item>
			</el-form>
		</div>
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
				fylx_id:'',
				fydy_name:'',
				fylb_id:'',
				fydw_id:'',
				jflx_id:'',
				qzfs_id:'',
				fydy_kjfp:1,
				fydy_ysr:1,
				fydy_cyskxm:[],
				fydy_zdr:'',
				fydy_ycys:0,
				fydy_wyjbl:0,
				fydy_remarks:'',
			},
			fylx_ids:[],
			fylb_ids:[],
			fydw_ids:[],
			jflx_ids:[],
			qzfs_ids:[],
			fydy_cyskxms:[],
			loading:false,
			rules: {
				fylx_id:[
					{required: true, message: '选择类型不能为空', trigger: 'change'},
				],
				fydy_name:[
					{required: true, message: '费用名称不能为空', trigger: 'blur'},
				],
				fylb_id:[
					{required: true, message: '选择类别不能为空', trigger: 'change'},
				],
				fydw_id:[
					{required: true, message: '费用单位不能为空', trigger: 'change'},
				],
				jflx_id:[
					{required: true, message: '计费类型不能为空', trigger: 'change'},
				],
				qzfs_id:[
					{required: true, message: '取整方式不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Fydy/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fylx_ids = res.data.data.fylx_ids
						this.fylb_ids = res.data.data.fylb_ids
						this.fydw_ids = res.data.data.fydw_ids
						this.jflx_ids = res.data.data.jflx_ids
						this.qzfs_ids = res.data.data.qzfs_ids
						this.fydy_cyskxms = res.data.data.fydy_cyskxms
					}
				})
			}
			if(val){
				this.open()
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.setDefaultVal('fydy_cyskxm')
			this.form.fydy_zdr = parseTime(this.form.fydy_zdr)
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Fydy/update',this.form).then(res => {
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
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
			this.$emit('changepage')
		},
	}
})
