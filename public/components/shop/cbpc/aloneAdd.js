Vue.component('Aloneadd', {
	template: `
		<el-dialog title="生成抄表" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="财务月份" prop="cbpc_cwyf">
							<el-date-picker value-format="yyyy-MM" type="month" v-model="form.cbpc_cwyf" clearable placeholder="请输入财务月份"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始日期" prop="cbpc_kstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.cbpc_kstime" clearable placeholder="请输入开始日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束日期" prop="cbpc_jstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.cbpc_jstime" clearable placeholder="请输入结束日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="楼座编号" prop="louyu_id">
							<el-select   style="width:100%" v-model="form.louyu_id" filterable clearable placeholder="请选择楼座编号">
								<el-option v-for="(item,i) in louyu_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="单元编号" prop="danyuan_id">
							<el-select   style="width:100%" v-model="form.danyuan_id" filterable clearable placeholder="请选择单元编号">
								<el-option v-for="(item,i) in danyuan_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房间编号" prop="fcxx_id">
							<el-select   style="width:100%" v-model="form.fcxx_id" filterable clearable placeholder="请选择房间编号">
								<el-option v-for="(item,i) in fcxx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="仪表类型" prop="yblx_id">
							<el-select   style="width:100%" v-model="form.yblx_id" filterable clearable placeholder="请选择仪表类型">
								<el-option v-for="(item,i) in yblx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="仪表种类" prop="ybzl_id">
							<el-select   style="width:100%" v-model="form.ybzl_id" filterable clearable placeholder="请选择仪表种类">
								<el-option v-for="(item,i) in ybzl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
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
	},
	data(){
		return {
			form: {
				shop_id:'',
				xqgl_id:'',
				cbpc_cwyf:'',
				cbpc_kstime:'',
				cbpc_jstime:'',
				louyu_id:'',
				danyuan_id:'',
				fcxx_id:'',
				yblx_id:'',
				ybzl_id:'',
				cbpc_ghcb:'2',
			},
			louyu_ids:[],
			danyuan_ids:[],
			fcxx_ids:[],
			yblx_ids:[],
			ybzl_ids:[],
			loading:false,
			rules: {
				cbpc_cwyf:[
					{required: true, message: '财务月份不能为空', trigger: 'blur'},
				],
				cbpc_kstime:[
					{required: true, message: '开始日期不能为空', trigger: 'blur'},
				],
				cbpc_jstime:[
					{required: true, message: '结束日期不能为空', trigger: 'blur'},
				],
				louyu_id:[
					{required: true, message: '楼座编号不能为空', trigger: 'change'},
				],
				danyuan_id:[
					{required: true, message: '单元编号不能为空', trigger: 'change'},
				],
				fcxx_id:[
					{required: true, message: '房间编号不能为空', trigger: 'change'},
				],
				yblx_id:[
					{required: true, message: '仪表类型不能为空', trigger: 'change'},
				],
				ybzl_id:[
					{required: true, message: '仪表种类不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Cbpc/getFieldList').then(res => {
					if(res.data.status == 200){
						this.louyu_ids = res.data.data.louyu_ids
						this.yblx_ids = res.data.data.yblx_ids
						this.ybzl_ids = res.data.data.ybzl_ids
					}
				})
			}
		},
		"form.louyu_id": "selectDanyuan_id",
		'form.danyuan_id': 'selectFcxx_id'
	},
	methods: {
		open(){
		},
		selectDanyuan_id(val){
			axios.post(base_url + '/Cbpc/getDanyuan_id',{louyu_id:val}).then(res => {
				if(res.data.status == 200){
					this.danyuan_ids = res.data.data
				}
			})
			console.log('danyuan_ids',this.danyuan_ids)
		},
		selectFcxx_id(val){
			axios.post(base_url + '/Cbpc/getFcxx_id',{danyuan_id:val}).then(res => {
				if(res.data.status == 200){
					this.fcxx_ids = res.data.data
				}
			})
			console.log('fcxx_ids',this.fcxx_ids)
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Cbpc/aloneAdd',this.form).then(res => {
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
