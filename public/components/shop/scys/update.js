Vue.component('Update', {
	template: `
		<el-dialog title="重新生成" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<!--<el-row >
					<el-col :span="24">
						<el-form-item label="生成楼宇" prop="louyu_id">
							<el-select   style="width:100%" v-model="form.louyu_id" filterable clearable placeholder="请选择生成楼宇">
								<el-option v-for="(item,i) in louyu_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>-->
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用种类" prop="fydy_id">
							<el-select multiple style="width:100%" v-model="form.fydy_id" filterable clearable placeholder="请选择费用种类">
								<el-option v-for="(item,i) in fydy_ids" :key="i" :label="item.key" :value="item.val.toString()"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="建筑类型" prop="jflx_id">
							<el-select   style="width:100%" v-model="form.jflx_id" filterable clearable placeholder="请选择建筑类型">
								<el-option v-for="(item,i) in jflx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="生成方式" prop="scys_scfs">
							<el-radio-group v-model="form.scys_scfs">
								<el-radio :label="1">按月生成</el-radio>
								<el-radio :label="2">按日生成</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.scys_scfs == 2">
					<el-col :span="24">
						<el-form-item label="生成类型" prop="scys_sclx">
							<el-radio-group v-model="form.scys_sclx">
								<el-radio :label="1">【按每月30天计算】</el-radio>
								<el-radio :label="2">【按每月实际天数计算】</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.scys_scfs == 1">
					<el-col :span="24">
						<el-form-item label="开始月份" prop="scys_ksyf">
							<el-date-picker value-format="yyyy-MM" type="month" v-model="form.scys_ksyf" clearable placeholder="请输入开始月份"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.scys_scfs == 1">
					<el-col :span="24">
						<el-form-item label="结束月份" prop="scys_jsyf">
							<el-date-picker value-format="yyyy-MM" type="month" v-model="form.scys_jsyf" clearable placeholder="请输入结束月份"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.scys_scfs == 2">
					<el-col :span="24">
						<el-form-item label="开始时间" prop="scys_kstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.scys_kstime" clearable placeholder="请输入开始时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.scys_scfs == 2">
					<el-col :span="24">
						<el-form-item label="终止时间" prop="scys_zztime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.scys_zztime" clearable placeholder="请输入终止时间"></el-date-picker>
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

				scys_ksyf:'',
				scys_jsyf:'',

				jflx_id:'',
				fydy_id:[],

				// louyu_id:'',

				scys_kstime:'',
				scys_zztime:'',

				scys_scfs:1,
				scys_sclx:1,
			},
			jflx_ids:[],
			louyu_ids:[],
			fydy_ids:[],
			loading:false,
			rules: {
				jflx_id:[
					{required: true, message: '建筑类型不能为空', trigger: 'change'},
				],
				fydy_id:[
					{required: true,type:'array', message: '费用种类不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Scys/getFieldList').then(res => {
					if(res.data.status == 200){
						this.jflx_ids = res.data.data.jflx_ids
						this.louyu_ids = res.data.data.louyu_ids
						this.fydy_ids = res.data.data.fydy_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.scys_kstime = parseTime(this.form.scys_kstime)
			this.form.scys_zztime = parseTime(this.form.scys_zztime)
			this.setDefaultVal('fydy_id')
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Scys/update',this.form).then(res => {
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
