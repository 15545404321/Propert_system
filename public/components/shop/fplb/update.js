Vue.component('Update', {
	template: `
		<el-drawer title="修改"  direction="rtl" size="1220px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用名称" prop="fydy_name">
							<el-input  v-model="form.fydy_name" autoComplete="off" clearable  placeholder="请输入费用名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用类别" prop="fydy_fylb">
							<el-select   style="width:100%" v-model="form.fydy_fylb" filterable clearable placeholder="请选择费用类别">
								<el-option v-for="(item,i) in fydy_fylbs" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用单位" prop="fydy_fydw">
							<el-select   style="width:100%" v-model="form.fydy_fydw" filterable clearable placeholder="请选择费用单位">
								<el-option v-for="(item,i) in fydy_fydws" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="计费类型" prop="fydy_jflx">
							<el-select   style="width:100%" v-model="form.fydy_jflx" filterable clearable placeholder="请选择计费类型">
								<el-option v-for="(item,i) in fydy_jflxs" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="取整方式" prop="fydy_qzfs">
							<el-select   style="width:100%" v-model="form.fydy_qzfs" filterable clearable placeholder="请选择取整方式">
								<el-option v-for="(item,i) in fydy_qzfss" :key="i" :label="item.key" :value="item.val"></el-option>
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
							<el-select style="width:100%" v-model="form.fydy_ysr" filterable clearable placeholder="请选择应收日份">
								<el-option key="0" label="应收月月末日期" :value="1"></el-option>
								<el-option key="1" label="指定日期" :value="0"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="冲预收款" prop="fydy_cyskxm">
							<el-select multiple style="width:100%" v-model="form.fydy_cyskxm" filterable clearable placeholder="请选择冲预收款">
								<el-option v-for="(item,i) in fydy_cyskxms" :key="i" :label="item.key" :value="item.val.toString()"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="指定日期" prop="fydy_zdr">
							<el-input  v-model="form.fydy_zdr" autoComplete="off" clearable  placeholder="请输入指定日期"></el-input>
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
						<el-form-item label="违约金比例" prop="fydy_wyjbl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fydy_wyjbl" clearable :min="0" placeholder="请输入违约金比例"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="备注信息" prop="fydy_remarks">
							<el-input  v-model="form.fydy_remarks" autoComplete="off" clearable  placeholder="请输入备注信息"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="排序" prop="fydy_px">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fydy_px" clearable :min="0" placeholder="请输入排序"/>
						</el-form-item>
					</el-col>
				</el-row>
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
				fylx_id:'',
				fydy_name:'',
				fydy_fylb:'',
				fydy_fydw:'',
				fydy_jflx:'',
				fydy_qzfs:'',
				fydy_kjfp:1,
				fydy_cyskxm:[],
				fydy_zdr:'',
				fydy_ycys:0,
				fydy_wyjbl:0,
				fydy_remarks:'',
				fydy_px:50,
			},
			fydy_fylbs:[],
			fydy_fydws:[],
			fydy_jflxs:[],
			fydy_qzfss:[],
			fydy_cyskxms:[],
			loading:false,
			rules: {
				fylx_id:[
					{required: true, message: '费用类型不能为空', trigger: ''},
				],
				fydy_name:[
					{required: true, message: '费用名称不能为空', trigger: 'blur'},
				],
				fydy_fydw:[
					{required: true, message: '费用单位不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Fplb/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fydy_fylbs = res.data.data.fydy_fylbs
						this.fydy_fydws = res.data.data.fydy_fydws
						this.fydy_jflxs = res.data.data.fydy_jflxs
						this.fydy_qzfss = res.data.data.fydy_qzfss
						this.fydy_cyskxms = res.data.data.fydy_cyskxms
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.setDefaultVal('fydy_cyskxm')
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Fplb/update',this.form).then(res => {
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
		},
	}
})
