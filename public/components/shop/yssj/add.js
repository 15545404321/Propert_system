Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用名称" prop="yssj_fymc">
							<el-input  v-model="form.yssj_fymc" autoComplete="off" clearable  placeholder="请输入费用名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="财务月份" prop="yssj_cwyf">
							<el-input  v-model="form.yssj_cwyf" autoComplete="off" clearable  placeholder="请输入财务月份"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始日期" prop="yssj_kstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.yssj_kstime" clearable placeholder="请输入开始日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="截至日期" prop="yssj_jztiem">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.yssj_jztiem" clearable placeholder="请输入截至日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用单价" prop="yssj_fydj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.yssj_fydj" clearable :min="0" placeholder="请输入费用单价"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="应收金额" prop="yssj_ysje">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.yssj_ysje" clearable :min="0" placeholder="请输入应收金额"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="违约金额" prop="yssj_wyje">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.yssj_wyje" clearable :min="0" placeholder="请输入违约金额"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="优惠金额" prop="yssj_yhje">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.yssj_yhje" clearable :min="0" placeholder="请输入优惠金额"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="实收金额" prop="yssj_shje">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.yssj_shje" clearable :min="0" placeholder="请输入实收金额"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用类型" prop="fylx_id">
							<el-select   style="width:100%" v-model="form.fylx_id" filterable clearable placeholder="请选择费用类型">
								<el-option v-for="(item,i) in fylx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用标准" prop="fybz_id">
							<el-select   style="width:100%" v-model="form.fybz_id" filterable clearable placeholder="请选择费用标准">
								<el-option v-for="(item,i) in fybz_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="付款状态" prop="yssj_stuats">
							<el-radio-group v-model="form.yssj_stuats">
								<el-radio :label="1">开启</el-radio>
								<el-radio :label="0">关闭</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="付款时间" prop="yssj_fksj">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.yssj_fksj" clearable placeholder="请输入付款时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="应收ID" prop="ys_id">
							<el-input  v-model="form.ys_id" autoComplete="off" clearable  placeholder="请输入应收ID"></el-input>
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
	},
	data(){
		return {
			form: {
				yssj_fymc:'',
				yssj_cwyf:'',
				yssj_kstime:'',
				yssj_jztiem:'',
				fylx_id:'',
				fybz_id:'',
				yssj_stuats:1,
				yssj_fksj:'',
				ys_id:'',
			},
			fylx_ids:[],
			fybz_ids:[],
			loading:false,
			rules: {
				yssj_fymc:[
					{required: true, message: '费用名称不能为空', trigger: 'blur'},
				],
				yssj_cwyf:[
					{required: true, message: '财务月份不能为空', trigger: 'blur'},
				],
				yssj_kstime:[
					{required: true, message: '开始日期不能为空', trigger: 'blur'},
				],
				yssj_jztiem:[
					{required: true, message: '截至日期不能为空', trigger: 'blur'},
				],
				yssj_fydj:[
					{required: true, message: '费用单价不能为空', trigger: 'blur'},
				],
				yssj_ysje:[
					{required: true, message: '应收金额不能为空', trigger: 'blur'},
				],
				yssj_wyje:[
					{required: true, message: '违约金额不能为空', trigger: 'blur'},
				],
				yssj_yhje:[
					{required: true, message: '优惠金额不能为空', trigger: 'blur'},
				],
				yssj_shje:[
					{required: true, message: '实收金额不能为空', trigger: 'blur'},
				],
				fylx_id:[
					{required: true, message: '费用类型不能为空', trigger: 'change'},
				],
				fybz_id:[
					{required: true, message: '费用标准不能为空', trigger: 'change'},
				],
				yssj_fksj:[
					{required: true, message: '付款时间不能为空', trigger: 'blur'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Yssj/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fylx_ids = res.data.data.fylx_ids
						this.fybz_ids = res.data.data.fybz_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Yssj/add',this.form).then(res => {
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
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
