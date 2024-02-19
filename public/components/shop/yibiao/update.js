Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="仪表编号" prop="yibiao_sn">
							<el-input  v-model="form.yibiao_sn" autoComplete="off" clearable  placeholder="请输入仪表编号"></el-input>
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
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.ybzl_id" :options="ybzl_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择仪表种类"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.yblx_id == 1">
					<el-col :span="24">
						<el-form-item label="楼宇/单元" prop="louyu_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.louyu_id" :options="louyu_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择楼宇/单元"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.yblx_id == 1">
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
						<el-form-item label="仪表倍率" prop="yibiao_ybbl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.yibiao_ybbl" clearable :min="0" placeholder="请输入仪表倍率"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="初始底数" prop="yibiao_csds">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.yibiao_csds" clearable :min="0" placeholder="请输入初始底数"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="仪表量程" prop="yibiao_yblc">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.yibiao_yblc" clearable :min="0" placeholder="请输入仪表量程"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="安装时间" prop="add_time">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.add_time" clearable placeholder="请输入安装时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="仪表状态" prop="yibiao_status">
							<el-radio-group v-model="form.yibiao_status">
								<el-radio :label="1">正常</el-radio>
								<el-radio :label="0">停用</el-radio>
								<el-radio :label="2">换表停用</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.yibiao_status == 2">
					<el-col :span="24">
						<el-form-item label="抄表数值" prop="cbgl_bqsl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.cbgl_bqsl" clearable :min="0" placeholder="请输入抄表数值"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.yibiao_status == 2">
					<el-col :span="24">
						<el-col :span="24">
							<el-form-item label="换表时间" prop="yibiao_hbsj">
								<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.yibiao_hbsj" clearable placeholder="请输入安装时间"></el-date-picker>
							</el-form-item>
						</el-col>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="仪表备注" prop="yibiao_remarks">
							<el-input  type="textarea" autoComplete="off" v-model="form.yibiao_remarks"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入备注"/>
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
		'treeselect':VueTreeselect.Treeselect,
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
				yibiao_sn:'',
				yblx_id:'',
				fcxx_id:'',
				yibiao_ybbl:1.00,
				yibiao_csds:0,
				yibiao_yblc:9999999,
				add_time:curentTime(),
				yibiao_hbsj:'',
				yibiao_remarks:'',
				yibiao_status:1,
			},
			yblx_ids:[],
			ybzl_ids:[],
			louyu_ids:[],
			fcxx_ids:[],
			loading:false,
			rules: {
				yibiao_sn:[
				],
				yblx_id:[
					{required: true, message: '仪表类型不能为空', trigger: 'change'},
				],
				ybzl_id:[
					{required: true, message: '仪表种类不能为空', trigger: 'change'},
				],
				yibiao_ybbl:[
					{required: true, message: '仪表倍率不能为空', trigger: 'blur'},
					{pattern:/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/, message: '仪表倍率格式错误'}
				],
				yibiao_csds:[
					{required: true, message: '初始底数不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '初始底数格式错误'}
				],
				yibiao_yblc:[
					{required: true, message: '仪表量程不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '仪表量程格式错误'}
				],
				add_time:[
					{required: true, message: '安装时间不能为空', trigger: 'blur'},
				],
				yibiao_status:[
					{required: true, message: '仪表状态不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/YiBiao/getFcxx_id',{louyu_id:this.info.louyu_id}).then(res => {
					if(res.data.status == 200){
						this.fcxx_ids = res.data.data
					}
				})
			}
			if(val){
				axios.post(base_url + '/YiBiao/getFieldList').then(res => {
					if(res.data.status == 200){
						this.yblx_ids = res.data.data.yblx_ids
						this.ybzl_ids = res.data.data.ybzl_ids
						this.louyu_ids = res.data.data.louyu_ids
					}
				})
			}
		},
		'form.louyu_id': 'selectFcxx_id'
	},
	methods: {
		open(){
			this.form = this.info
			console.log('form',this.form)
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
			this.form.add_time = parseTime(this.form.add_time)
		},
		selectFcxx_id(val){
			this.form.fcxx_id = ''
			axios.post(base_url + '/YiBiao/getFcxx_id',{louyu_id:val}).then(res => {
				if(res.data.status == 200){
					this.fcxx_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/YiBiao/update',this.form).then(res => {
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
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
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
